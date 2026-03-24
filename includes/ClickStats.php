<?php
declare(strict_types=1);

namespace FP\CtaBar;

/**
 * Gestione statistiche click CTA Bar (aggregazione su wp_options).
 */
final class ClickStats {

    private const OPTION_KEY = 'fp_cta_bar_click_stats';
    private const MAX_URL_LENGTH = 2048;
    private const MAX_LABEL_LENGTH = 500;
    private const MAX_LANG_LENGTH = 10;
    private const MAX_ROWS = 300;

    private static ?self $instance = null;

    /**
     * Restituisce l'istanza singleton.
     */
    public static function get_instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Registra gli hook runtime.
     */
    private function __construct() {
        add_action('fp_cta_bar_clicked', [$this, 'track_click'], 10, 3);
    }

    /**
     * Registra un click su URL/label/lingua in forma aggregata.
     *
     * @param string $url URL cliccata.
     * @param string $label Etichetta tracciata.
     * @param string $lang Codice lingua.
     * @return void
     */
    public function track_click(string $url, string $label = '', string $lang = ''): void {
        $url = esc_url_raw(mb_substr($url, 0, self::MAX_URL_LENGTH));
        if ($url === '') {
            return;
        }

        $label = sanitize_text_field(mb_substr($label, 0, self::MAX_LABEL_LENGTH));
        $lang  = sanitize_text_field(mb_substr($lang, 0, self::MAX_LANG_LENGTH));
        $date  = gmdate('Y-m-d');
        $now   = gmdate('Y-m-d H:i:s');

        $stats = $this->get_stats();
        $key   = md5($url . '|' . $label . '|' . $lang);

        $stats['total_clicks'] = (int) ($stats['total_clicks'] ?? 0) + 1;
        $stats['updated_at']   = $now;

        if (!isset($stats['by_day'][$date])) {
            $stats['by_day'][$date] = 0;
        }
        $stats['by_day'][$date] = (int) $stats['by_day'][$date] + 1;

        if (!isset($stats['rows'][$key]) || !is_array($stats['rows'][$key])) {
            $stats['rows'][$key] = [
                'url'           => $url,
                'label'         => $label,
                'lang'          => $lang,
                'clicks'        => 0,
                'last_clicked'  => '',
                'first_clicked' => $now,
                'by_day'        => [],
            ];
        }

        $stats['rows'][$key]['clicks'] = (int) ($stats['rows'][$key]['clicks'] ?? 0) + 1;
        $stats['rows'][$key]['last_clicked'] = $now;

        if (!isset($stats['rows'][$key]['by_day'][$date])) {
            $stats['rows'][$key]['by_day'][$date] = 0;
        }
        $stats['rows'][$key]['by_day'][$date] = (int) $stats['rows'][$key]['by_day'][$date] + 1;

        // Mantiene solo le righe più cliccate/attive per evitare crescita infinita dell'option.
        if (count($stats['rows']) > self::MAX_ROWS) {
            uasort($stats['rows'], static function (array $a, array $b): int {
                $clicksA = (int) ($a['clicks'] ?? 0);
                $clicksB = (int) ($b['clicks'] ?? 0);
                if ($clicksA === $clicksB) {
                    return strcmp((string) ($b['last_clicked'] ?? ''), (string) ($a['last_clicked'] ?? ''));
                }

                return $clicksB <=> $clicksA;
            });
            $stats['rows'] = array_slice($stats['rows'], 0, self::MAX_ROWS, true);
        }

        update_option(self::OPTION_KEY, $stats, false);
    }

    /**
     * Restituisce statistiche normalizzate.
     *
     * @return array<string,mixed>
     */
    public function get_stats(): array {
        $raw = get_option(self::OPTION_KEY, []);

        if (!is_array($raw)) {
            $raw = [];
        }

        $raw['total_clicks'] = (int) ($raw['total_clicks'] ?? 0);
        $raw['updated_at']   = (string) ($raw['updated_at'] ?? '');
        $raw['by_day']       = is_array($raw['by_day'] ?? null) ? $raw['by_day'] : [];
        $raw['rows']         = is_array($raw['rows'] ?? null) ? $raw['rows'] : [];

        return $raw;
    }

    /**
     * Restituisce i click totali di oggi.
     */
    public function get_today_clicks(): int {
        $stats = $this->get_stats();
        $today = gmdate('Y-m-d');

        return (int) ($stats['by_day'][$today] ?? 0);
    }

    /**
     * Restituisce i click ultimi N giorni (incluso oggi).
     *
     * @param int $days Giorni da includere.
     */
    public function get_clicks_last_days(int $days): int {
        $days = max(1, $days);
        $stats = $this->get_stats();
        $sum = 0;

        for ($i = 0; $i < $days; $i++) {
            $day = gmdate('Y-m-d', strtotime('-' . $i . ' days'));
            $sum += (int) ($stats['by_day'][$day] ?? 0);
        }

        return $sum;
    }

    /**
     * Restituisce righe ordinate per click decrescente.
     *
     * @param int $limit Massimo numero righe.
     * @return array<int,array<string,mixed>>
     */
    public function get_rows(int $limit = 100): array {
        $stats = $this->get_stats();
        $rows  = array_values($stats['rows']);

        usort($rows, static function (array $a, array $b): int {
            $clicksA = (int) ($a['clicks'] ?? 0);
            $clicksB = (int) ($b['clicks'] ?? 0);
            if ($clicksA === $clicksB) {
                return strcmp((string) ($b['last_clicked'] ?? ''), (string) ($a['last_clicked'] ?? ''));
            }

            return $clicksB <=> $clicksA;
        });

        if ($limit <= 0) {
            return [];
        }

        return array_slice($rows, 0, $limit);
    }

    /**
     * Azzera completamente le statistiche.
     *
     * @return void
     */
    public function reset_stats(): void {
        delete_option(self::OPTION_KEY);
    }
}
