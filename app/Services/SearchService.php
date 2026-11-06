<?php
/**
 * Multan Tech Accessories — SearchService
 * Ultra-robust, space-resilient, multi-token e-commerce search & ranking engine.
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class SearchService
{
    /**
     * Normalize string by stripping non-alphanumeric characters and lowercasing.
     * E.g. "SPEEDX SP-S188L" => "speedxsps188l"
     *      "speed x sp"      => "speedxsp"
     */
    public static function normalize(string $str): string
    {
        $lower = mb_strtolower($str, 'UTF-8');
        return preg_replace('/[^a-z0-9]/', '', $lower) ?? '';
    }

    /**
     * Tokenize query into clean individual words.
     */
    public static function tokenize(string $query): array
    {
        $clean = mb_strtolower(trim($query), 'UTF-8');
        $clean = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $clean);
        $parts = preg_split('/\s+/', trim($clean), -1, PREG_SPLIT_NO_EMPTY);
        return $parts ?: [];
    }

    /**
     * Generate variations of token combinations:
     * - Raw tokens (e.g. ['speed', 'x', 'sp'])
     * - Adjacent short token merges (e.g. ['speedx', 'sp'])
     * - Pairwise merges (e.g. ['power', 'bank'] => ['powerbank'])
     * - Full compact merge (e.g. ['speedxsp'])
     */
    public static function generateTokenSets(array $rawTokens): array
    {
        $count = count($rawTokens);
        if ($count === 0) {
            return [];
        }
        $sets = [$rawTokens];

        // 1. Adjacent short token merges (handles "speed x", "type c", "gan 5", "sp s188")
        $merged = [];
        $i = 0;
        while ($i < $count) {
            if ($i < $count - 1 && (strlen($rawTokens[$i]) <= 2 || strlen($rawTokens[$i + 1]) <= 2)) {
                $merged[] = $rawTokens[$i] . $rawTokens[$i + 1];
                $i += 2;
            } else {
                $merged[] = $rawTokens[$i];
                $i++;
            }
        }
        if ($merged !== $rawTokens && !empty($merged)) {
            $sets[] = $merged;
        }

        // 2. Pairwise compound word merges (e.g. "power bank" -> "powerbank", "air mouse" -> "airmouse")
        if ($count >= 2) {
            $pairMerged = [];
            for ($j = 0; $j < $count; $j++) {
                if ($j < $count - 1) {
                    $pairMerged[] = $rawTokens[$j] . $rawTokens[$j + 1];
                    $j++;
                } else {
                    $pairMerged[] = $rawTokens[$j];
                }
            }
            if ($pairMerged !== $rawTokens && $pairMerged !== $merged && !empty($pairMerged)) {
                $sets[] = $pairMerged;
            }
        }

        // 3. Full compact merge for short queries (<= 4 words)
        if ($count <= 4 && $count > 1) {
            $sets[] = [implode('', $rawTokens)];
        }

        return $sets;
    }

    /**
     * Storefront product search with pagination and intelligent ranking.
     *
     * @param string $query
     * @param int $limit
     * @param int $offset
     * @return array{items: array, total: int}
     */
    public static function search(string $query, int $limit = 24, int $offset = 0): array
    {
        $cleanQuery = mb_strtolower(trim($query), 'UTF-8');
        if ($cleanQuery === '') {
            return ['items' => [], 'total' => 0];
        }

        $candidates = self::fetchStorefrontCandidates($cleanQuery);
        $scored = self::rankCandidates($candidates, $cleanQuery, false);

        $total = count($scored);
        $items = array_slice($scored, $offset, $limit);

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    /**
     * Live search suggestions dropdown endpoint.
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public static function suggestions(string $query, int $limit = 8): array
    {
        $cleanQuery = mb_strtolower(trim($query), 'UTF-8');
        if (mb_strlen($cleanQuery) < 2) {
            return [];
        }

        $res = self::search($cleanQuery, $limit, 0);
        $output = [];

        foreach ($res['items'] as $item) {
            $img = !empty($item['image_400'])
                ? (string)$item['image_400']
                : (!empty($item['primary_image_url']) ? (string)$item['primary_image_url'] : '/assets/images/placeholder.svg');

            $output[] = [
                'id' => (int)$item['id'],
                'title' => (string)$item['title'],
                'slug' => (string)$item['slug'],
                'sell_price' => (float)$item['sell_price'],
                'primary_image_url' => (string)($item['primary_image_url'] ?? ''),
                'image_400' => (string)($item['image_400'] ?? ''),
                'image' => $img,
                'brand_name' => (string)($item['brand_name'] ?? ''),
            ];
        }

        return $output;
    }

    /**
     * Admin product catalog search with filter support.
     *
     * @param string $query
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array{items: array, total: int}
     */
    public static function searchAdmin(string $query, array $filters = [], int $limit = 25, int $offset = 0): array
    {
        $cleanQuery = mb_strtolower(trim($query), 'UTF-8');
        $db = Database::getInstance();

        // If search query is empty, run standard SQL filtering with pagination
        if ($cleanQuery === '') {
            $where = ['1=1'];
            $params = [];

            if (!empty($filters['category_id']) && (int)$filters['category_id'] > 0) {
                $where[] = 'p.category_id = ?';
                $params[] = (int)$filters['category_id'];
            }
            if (!empty($filters['stock_status'])) {
                $where[] = 'p.stock_status = ?';
                $params[] = (string)$filters['stock_status'];
            }
            if (!empty($filters['content_state'])) {
                $where[] = 'p.content_state = ?';
                $params[] = (string)$filters['content_state'];
            }

            $whereSql = implode(' AND ', $where);
            $countRow = $db->fetch("SELECT COUNT(p.id) as total FROM products p WHERE {$whereSql}", $params);
            $total = (int)($countRow['total'] ?? 0);

            $items = $db->fetchAll(
                "SELECT p.*, b.name AS brand_name, c.name AS category_name
                 FROM products p
                 LEFT JOIN brands b ON b.id = p.brand_id
                 LEFT JOIN categories c ON c.id = p.category_id
                 WHERE {$whereSql}
                 ORDER BY p.id DESC
                 LIMIT " . (int)$limit . " OFFSET " . (int)$offset,
                $params
            );

            return ['items' => $items, 'total' => $total];
        }

        // Search query provided: fetch candidates matching admin filters
        $candidates = self::fetchAdminCandidates($cleanQuery, $filters);
        $scored = self::rankCandidates($candidates, $cleanQuery, true);

        $total = count($scored);
        $items = array_slice($scored, $offset, $limit);

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    /**
     * Candidate fetching for storefront products.
     */
    private static function fetchStorefrontCandidates(string $cleanQuery): array
    {
        $db = Database::getInstance();
        $rawTokens = self::tokenize($cleanQuery);
        $normQuery = self::normalize($cleanQuery);

        $candidateTokens = [];
        foreach ($rawTokens as $t) {
            if (strlen($t) >= 2) {
                $candidateTokens[] = $t;
            }
        }
        for ($i = 0; $i < count($rawTokens) - 1; $i++) {
            $candidateTokens[] = $rawTokens[$i] . $rawTokens[$i + 1];
        }
        $candidateTokens = array_unique($candidateTokens);

        $whereOr = [];
        $params = [];

        // 1. Normalized match (space/hyphen-insensitive)
        if (strlen($normQuery) >= 2) {
            $whereOr[] = "REPLACE(REPLACE(REPLACE(LOWER(p.title), ' ', ''), '-', ''), '_', '') LIKE ?";
            $params[] = '%' . $normQuery . '%';
            $whereOr[] = "REPLACE(REPLACE(REPLACE(LOWER(p.sku), ' ', ''), '-', ''), '_', '') LIKE ?";
            $params[] = '%' . $normQuery . '%';
        }

        // 2. Exact phrase
        $whereOr[] = "p.title LIKE ?";
        $params[] = '%' . $cleanQuery . '%';

        // 3. Candidate tokens & merged pairs
        foreach ($candidateTokens as $tok) {
            if (strlen($tok) >= 2) {
                $whereOr[] = "p.title LIKE ?";
                $params[] = '%' . $tok . '%';
                $whereOr[] = "b.name LIKE ?";
                $params[] = '%' . $tok . '%';
                $whereOr[] = "p.sku LIKE ?";
                $params[] = '%' . $tok . '%';
            }
        }

        $sql = "SELECT p.*, b.name AS brand_name, c.name AS category_name
                FROM products p
                LEFT JOIN brands b ON b.id = p.brand_id
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.is_active = 1 AND p.is_published = 1";

        if (!empty($whereOr)) {
            $sql .= " AND (" . implode(' OR ', $whereOr) . ")";
        }

        $candidates = $db->fetchAll($sql, $params);

        // Fallback: If candidate query returned fewer than 5 rows and query has 2+ words, fetch broad catalog
        if (count($candidates) < 5 && count($rawTokens) >= 2) {
            $broadSql = "SELECT p.*, b.name AS brand_name, c.name AS category_name
                         FROM products p
                         LEFT JOIN brands b ON b.id = p.brand_id
                         LEFT JOIN categories c ON c.id = p.category_id
                         WHERE p.is_active = 1 AND p.is_published = 1";
            $candidates = $db->fetchAll($broadSql);
        }

        return $candidates;
    }

    /**
     * Candidate fetching for admin catalog with filters.
     */
    private static function fetchAdminCandidates(string $cleanQuery, array $filters = []): array
    {
        $db = Database::getInstance();
        $rawTokens = self::tokenize($cleanQuery);
        $normQuery = self::normalize($cleanQuery);

        $candidateTokens = [];
        foreach ($rawTokens as $t) {
            if (strlen($t) >= 2) {
                $candidateTokens[] = $t;
            }
        }
        for ($i = 0; $i < count($rawTokens) - 1; $i++) {
            $candidateTokens[] = $rawTokens[$i] . $rawTokens[$i + 1];
        }
        $candidateTokens = array_unique($candidateTokens);

        $whereAnd = ['1=1'];
        $params = [];

        if (!empty($filters['category_id']) && (int)$filters['category_id'] > 0) {
            $whereAnd[] = 'p.category_id = ?';
            $params[] = (int)$filters['category_id'];
        }
        if (!empty($filters['stock_status'])) {
            $whereAnd[] = 'p.stock_status = ?';
            $params[] = (string)$filters['stock_status'];
        }
        if (!empty($filters['content_state'])) {
            $whereAnd[] = 'p.content_state = ?';
            $params[] = (string)$filters['content_state'];
        }

        $whereOr = [];
        if (strlen($normQuery) >= 2) {
            $whereOr[] = "REPLACE(REPLACE(REPLACE(LOWER(p.title), ' ', ''), '-', ''), '_', '') LIKE ?";
            $params[] = '%' . $normQuery . '%';
            $whereOr[] = "REPLACE(REPLACE(REPLACE(LOWER(p.sku), ' ', ''), '-', ''), '_', '') LIKE ?";
            $params[] = '%' . $normQuery . '%';
            $whereOr[] = "REPLACE(REPLACE(REPLACE(LOWER(p.supplier_id), ' ', ''), '-', ''), '_', '') LIKE ?";
            $params[] = '%' . $normQuery . '%';
        }

        $whereOr[] = "p.title LIKE ?";
        $params[] = '%' . $cleanQuery . '%';

        foreach ($candidateTokens as $tok) {
            if (strlen($tok) >= 2) {
                $whereOr[] = "p.title LIKE ?";
                $params[] = '%' . $tok . '%';
                $whereOr[] = "b.name LIKE ?";
                $params[] = '%' . $tok . '%';
                $whereOr[] = "p.sku LIKE ?";
                $params[] = '%' . $tok . '%';
                $whereOr[] = "p.supplier_id LIKE ?";
                $params[] = '%' . $tok . '%';
            }
        }

        if (!empty($whereOr)) {
            $whereAnd[] = '(' . implode(' OR ', $whereOr) . ')';
        }

        $sql = "SELECT p.*, b.name AS brand_name, c.name AS category_name
                FROM products p
                LEFT JOIN brands b ON b.id = p.brand_id
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE " . implode(' AND ', $whereAnd);

        $candidates = $db->fetchAll($sql, $params);

        if (count($candidates) < 5 && count($rawTokens) >= 2) {
            $broadParams = [];
            $broadAnd = ['1=1'];
            if (!empty($filters['category_id']) && (int)$filters['category_id'] > 0) {
                $broadAnd[] = 'p.category_id = ?';
                $broadParams[] = (int)$filters['category_id'];
            }
            if (!empty($filters['stock_status'])) {
                $broadAnd[] = 'p.stock_status = ?';
                $broadParams[] = (string)$filters['stock_status'];
            }
            if (!empty($filters['content_state'])) {
                $broadAnd[] = 'p.content_state = ?';
                $broadParams[] = (string)$filters['content_state'];
            }

            $broadSql = "SELECT p.*, b.name AS brand_name, c.name AS category_name
                         FROM products p
                         LEFT JOIN brands b ON b.id = p.brand_id
                         LEFT JOIN categories c ON c.id = p.category_id
                         WHERE " . implode(' AND ', $broadAnd);
            $candidates = $db->fetchAll($broadSql, $broadParams);
        }

        return $candidates;
    }

    /**
     * Precision ranking and scoring algorithm.
     */
    private static function rankCandidates(array $candidates, string $cleanQuery, bool $isAdmin = false): array
    {
        $rawTokens = self::tokenize($cleanQuery);
        $tokenCount = count($rawTokens);
        $normQuery = self::normalize($cleanQuery);

        if ($normQuery === '') {
            return [];
        }

        $tokenSets = self::generateTokenSets($rawTokens);

        $scored = [];
        $hasFullMatch = false;

        foreach ($candidates as $p) {
            $title = (string)($p['title'] ?? '');
            $brand = (string)($p['brand_name'] ?? '');
            $sku = (string)($p['sku'] ?? '');
            $suppId = (string)($p['supplier_id'] ?? '');
            $desc = (string)($p['description_html'] ?? '');

            $lowerTitle = mb_strtolower($title, 'UTF-8');
            $lowerBrand = mb_strtolower($brand, 'UTF-8');
            $lowerSku = mb_strtolower($sku, 'UTF-8');
            $lowerSuppId = mb_strtolower($suppId, 'UTF-8');
            $lowerDesc = mb_strtolower(strip_tags($desc), 'UTF-8');

            $normTitle = self::normalize($lowerTitle);
            $normBrand = self::normalize($lowerBrand);
            $normSku = self::normalize($lowerSku);
            $normSuppId = self::normalize($lowerSuppId);

            $score = 0;
            $isFullMatch = false;

            // 1. Exact full title, SKU, or supplier ID match (Highest priority)
            if ($lowerTitle === $cleanQuery || $lowerSku === $cleanQuery || $lowerSuppId === $cleanQuery) {
                $score += 5000;
                $isFullMatch = true;
            }

            // 2. Exact phrase inside title
            if (strpos($lowerTitle, $cleanQuery) !== false) {
                $score += 1600;
                if (strpos($lowerTitle, $cleanQuery) === 0) {
                    $score += 400; // Title starts with query
                }
                $isFullMatch = true;
            }

            // 3. Continuous normalized substring match
            // Handles spacing variations: "speed x sp" -> "speedxsp" matching inside "speedxsps188l..."
            if (strlen($normQuery) >= 2 && strpos($normTitle, $normQuery) !== false) {
                $score += 1300;
                if (strpos($normTitle, $normQuery) === 0) {
                    $score += 500; // Starts with normalized query
                }
                $isFullMatch = true;
            } elseif (strlen($normQuery) >= 2 && (strpos($normSku, $normQuery) !== false || strpos($normBrand, $normQuery) !== false || strpos($normSuppId, $normQuery) !== false)) {
                $score += 800;
                $isFullMatch = true;
            }

            // 4. Multi-token evaluation across candidate sets
            $bestTokenScore = 0;
            $bestRatio = 0.0;

            foreach ($tokenSets as $tokens) {
                $count = count($tokens);
                if ($count === 0) continue;

                $matchedTokens = 0;
                $tScore = 0;

                foreach ($tokens as $tok) {
                    $len = strlen($tok);
                    if ($len === 0) continue;

                    // Single character: isolated/word-bounded only
                    if ($len === 1) {
                        if (preg_match('/\b' . preg_quote($tok, '/') . '\b/i', $title)) {
                            $matchedTokens++;
                            $tScore += 50;
                        }
                        continue;
                    }

                    $tokNorm = self::normalize($tok);
                    $found = false;

                    if (strpos($lowerTitle, $tok) !== false) {
                        $found = true;
                        $tScore += 120;
                    } elseif ($tokNorm !== '' && strpos($normTitle, $tokNorm) !== false) {
                        $found = true;
                        $tScore += 100;
                    } elseif (strpos($lowerBrand, $tok) !== false || ($tokNorm !== '' && strpos($normBrand, $tokNorm) !== false)) {
                        $found = true;
                        $tScore += 90;
                    } elseif (strpos($lowerSku, $tok) !== false || ($tokNorm !== '' && strpos($normSku, $tokNorm) !== false)) {
                        $found = true;
                        $tScore += 90;
                    } elseif ($isAdmin && (strpos($lowerSuppId, $tok) !== false || ($tokNorm !== '' && strpos($normSuppId, $tokNorm) !== false))) {
                        $found = true;
                        $tScore += 90;
                    } elseif (strpos($lowerDesc, $tok) !== false) {
                        $found = true;
                        $tScore += 30;
                    }

                    if ($found) {
                        $matchedTokens++;
                    }
                }

                $ratio = $matchedTokens / $count;
                if ($ratio > $bestRatio) {
                    $bestRatio = $ratio;
                }

                // 100% token coverage in set
                if ($matchedTokens === $count) {
                    $tScore += (600 * $count);
                    $isFullMatch = true;
                }

                if ($tScore > $bestTokenScore) {
                    $bestTokenScore = $tScore;
                }
            }

            // Eligibility gate
            if (!$isFullMatch) {
                if ($tokenCount <= 2 && $bestRatio < 1.0) {
                    continue;
                }
                if ($tokenCount >= 3 && $bestRatio < 0.6) {
                    continue;
                }
            }

            $score += $bestTokenScore;

            // Stock status weighting
            $status = (int)($p['status'] ?? 1);
            if ($status === 1) {
                $score += 60; // In stock bonus
            } elseif ($status === 3) {
                $score += 20; // Coming soon
            } else {
                $score -= 40; // Out of stock
            }
            $score += (int)min(50, ($p['popularity_score'] ?? 0) * 0.5);

            if ($score > 0) {
                if ($isFullMatch) {
                    $hasFullMatch = true;
                }
                $p['calc_score'] = $score;
                $p['is_full_match'] = $isFullMatch;
                $scored[] = $p;
            }
        }

        // Prioritize full matches strictly
        if ($hasFullMatch) {
            $scored = array_filter($scored, fn($x) => !empty($x['is_full_match']));
        }

        usort($scored, function ($a, $b) use ($isAdmin) {
            if ($b['calc_score'] !== $a['calc_score']) {
                return $b['calc_score'] <=> $a['calc_score'];
            }
            if (!$isAdmin) {
                $statusA = (int)($a['status'] ?? 1);
                $statusB = (int)($b['status'] ?? 1);
                if ($statusA !== $statusB) {
                    return $statusA <=> $statusB;
                }
                $popA = (int)($a['popularity_score'] ?? 0);
                $popB = (int)($b['popularity_score'] ?? 0);
                if ($popB !== $popA) {
                    return $popB <=> $popA;
                }
            }
            return (int)($b['id'] ?? 0) <=> (int)($a['id'] ?? 0);
        });

        return $scored;
    }
}
