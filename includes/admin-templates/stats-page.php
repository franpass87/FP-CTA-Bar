<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$stats_service = \FP\CtaBar\ClickStats::get_instance();
$stats_data = $stats_service->get_stats();
$rows = $stats_service->get_rows(100);

$total_clicks = (int) ($stats_data['total_clicks'] ?? 0);
$today_clicks = $stats_service->get_today_clicks();
$last_7_days = $stats_service->get_clicks_last_days(7);
$updated_at = (string) ($stats_data['updated_at'] ?? '');
$updated_at_display = $updated_at !== '' ? get_date_from_gmt($updated_at, 'd/m/Y H:i') : __('Mai', 'fp-cta-bar');

$stats_reset = isset($_GET['stats_reset']) && sanitize_text_field(wp_unslash((string) $_GET['stats_reset'])) === '1';
?>
<div class="wrap fpctabar-admin-page fpctabar-stats-page">
    <h1 class="screen-reader-text"><?php esc_html_e('FP CTA Bar - Statistiche', 'fp-cta-bar'); ?></h1>

    <div class="fpctabar-page-header">
        <div class="fpctabar-page-header-content">
            <h2 class="fpctabar-page-header-title" aria-hidden="true"><span class="dashicons dashicons-chart-bar"></span> <?php esc_html_e('Statistiche Click CTA', 'fp-cta-bar'); ?></h2>
            <p><?php esc_html_e('Panoramica dei click tracciati sui link del pannello CTA.', 'fp-cta-bar'); ?></p>
        </div>
        <span class="fpctabar-page-header-badge">v<?php echo esc_html(FP_CTA_BAR_VERSION); ?></span>
    </div>

    <?php if ($stats_reset) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e('Statistiche azzerate correttamente.', 'fp-cta-bar'); ?></p>
        </div>
    <?php endif; ?>

    <div class="fpctabar-status-bar">
        <span class="fpctabar-status-pill is-active">
            <span class="dot"></span>
            <?php echo esc_html(sprintf(__('Click totali: %d', 'fp-cta-bar'), $total_clicks)); ?>
        </span>
        <span class="fpctabar-status-pill <?php echo $today_clicks > 0 ? 'is-active' : 'is-missing'; ?>">
            <span class="dot"></span>
            <?php echo esc_html(sprintf(__('Oggi: %d', 'fp-cta-bar'), $today_clicks)); ?>
        </span>
        <span class="fpctabar-status-pill <?php echo $last_7_days > 0 ? 'is-active' : 'is-missing'; ?>">
            <span class="dot"></span>
            <?php echo esc_html(sprintf(__('Ultimi 7 giorni: %d', 'fp-cta-bar'), $last_7_days)); ?>
        </span>
        <span class="fpctabar-status-pill">
            <span class="dot"></span>
            <?php echo esc_html(sprintf(__('Ultimo aggiornamento: %s', 'fp-cta-bar'), $updated_at_display)); ?>
        </span>
    </div>

    <div class="fpctabar-card">
        <div class="fpctabar-card-header">
            <div class="fpctabar-card-header-left">
                <span class="dashicons dashicons-admin-links"></span>
                <h2><?php esc_html_e('Top link cliccati', 'fp-cta-bar'); ?></h2>
            </div>
            <span class="fpctabar-badge fpctabar-badge-neutral"><?php echo esc_html(sprintf(_n('%d riga', '%d righe', count($rows), 'fp-cta-bar'), count($rows))); ?></span>
        </div>
        <div class="fpctabar-card-body">
            <?php if (empty($rows)) : ?>
                <p class="description"><?php esc_html_e('Nessun click registrato al momento. I click vengono salvati quando è attiva l\'opzione "Traccia click" sul singolo link.', 'fp-cta-bar'); ?></p>
            <?php else : ?>
                <div class="fpctabar-stats-table-wrap">
                    <table class="widefat striped fpctabar-stats-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Label', 'fp-cta-bar'); ?></th>
                                <th><?php esc_html_e('URL', 'fp-cta-bar'); ?></th>
                                <th><?php esc_html_e('Lingua', 'fp-cta-bar'); ?></th>
                                <th><?php esc_html_e('Click', 'fp-cta-bar'); ?></th>
                                <th><?php esc_html_e('Primo click', 'fp-cta-bar'); ?></th>
                                <th><?php esc_html_e('Ultimo click', 'fp-cta-bar'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row) : ?>
                                <?php
                                $label = trim((string) ($row['label'] ?? ''));
                                $url = (string) ($row['url'] ?? '');
                                $lang = trim((string) ($row['lang'] ?? ''));
                                $clicks = (int) ($row['clicks'] ?? 0);
                                $first = (string) ($row['first_clicked'] ?? '');
                                $last = (string) ($row['last_clicked'] ?? '');
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $label !== '' ? esc_html($label) : '<em>' . esc_html__('(senza label)', 'fp-cta-bar') . '</em>'; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($url); ?></a>
                                    </td>
                                    <td><?php echo esc_html($lang !== '' ? strtoupper($lang) : '-'); ?></td>
                                    <td><strong><?php echo esc_html((string) $clicks); ?></strong></td>
                                    <td><?php echo esc_html($first !== '' ? get_date_from_gmt($first, 'd/m/Y H:i') : '-'); ?></td>
                                    <td><?php echo esc_html($last !== '' ? get_date_from_gmt($last, 'd/m/Y H:i') : '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="fpctabar-card">
        <div class="fpctabar-card-header">
            <div class="fpctabar-card-header-left">
                <span class="dashicons dashicons-trash"></span>
                <h2><?php esc_html_e('Reset statistiche', 'fp-cta-bar'); ?></h2>
            </div>
        </div>
        <div class="fpctabar-card-body">
            <p class="description"><?php esc_html_e('Questa azione elimina tutte le statistiche click salvate da FP CTA Bar.', 'fp-cta-bar'); ?></p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="fp_cta_bar_reset_stats">
                <?php wp_nonce_field('fp_cta_bar_reset_stats'); ?>
                <button type="submit" class="fpctabar-btn fpctabar-btn-secondary" onclick="return confirm('<?php echo esc_js(__('Confermi il reset completo delle statistiche click?', 'fp-cta-bar')); ?>');">
                    <span class="dashicons dashicons-trash"></span> <?php esc_html_e('Azzera statistiche', 'fp-cta-bar'); ?>
                </button>
            </form>
        </div>
    </div>
</div>
