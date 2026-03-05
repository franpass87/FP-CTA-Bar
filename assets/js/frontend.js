(function () {
    'use strict';

    var container = document.getElementById('fpctabar');
    if (!container) return;

    var dismissHours = parseInt(container.getAttribute('data-dismiss-hours') || '0', 10);
    if (dismissHours > 0) {
        try {
            var dismissedAt = parseInt(localStorage.getItem('fpctabar_dismissed') || '0', 10);
            if (dismissedAt && (Date.now() - dismissedAt) < dismissHours * 3600 * 1000) {
                container.style.display = 'none';
                return;
            }
        } catch (e) {}
    }

    var mode = container.getAttribute('data-mode');
    var trigger = container.querySelector('.fpctabar__bar, .fpctabar__btn');
    var panel = document.getElementById('fpctabar-panel');
    var closeOnLinkClick = container.getAttribute('data-close-on-link-click') !== '0';
    var delay = parseInt(container.getAttribute('data-delay') || '0', 10);
    var scrollPercent = parseInt(container.getAttribute('data-scroll-percent') || '0', 10);
    var panelOpenDefault = container.getAttribute('data-panel-open') === '1';
    var animation = container.getAttribute('data-animation') || 'slide';

    if (!trigger || !panel) return;

    var isOpen = panelOpenDefault;

    if (animation && animation !== 'none') {
        container.classList.add('fpctabar--anim-' + animation);
    }

    if (mode === 'fullwidth') {
        document.body.classList.add('fpctabar-body--fullwidth');
    }

    if (panelOpenDefault) {
        container.classList.add('fpctabar--open');
        trigger.setAttribute('aria-expanded', 'true');
        panel.setAttribute('aria-hidden', 'false');
    }

    var visible = !delay && !scrollPercent;
    if (!visible) {
        container.classList.add('fpctabar--hidden');
    }

    function show() {
        if (visible) return;
        visible = true;
        container.classList.remove('fpctabar--hidden');
    }

    if (delay > 0) {
        setTimeout(show, delay * 1000);
    } else if (scrollPercent > 0) {
        var scrollHandler = function () {
            var h = document.documentElement.scrollHeight - window.innerHeight;
            if (h <= 0) {
                show();
                return;
            }
            var pct = (window.scrollY / h) * 100;
            if (pct >= scrollPercent) {
                show();
                window.removeEventListener('scroll', scrollHandler, { passive: true });
            }
        };
        window.addEventListener('scroll', scrollHandler, { passive: true });
        scrollHandler();
    }

    function track(action, label) {
        var cfg = typeof fpCtaBarTrack !== 'undefined' ? fpCtaBarTrack : {};
        if (cfg.ga4 && cfg.ga4.enabled && typeof gtag === 'function') {
            gtag('event', cfg.ga4.eventName, { event_action: action, event_label: label || '' });
        }
        if (cfg.gtm && cfg.gtm.enabled) {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: cfg.gtm.eventName,
                cta_bar_action: action,
                cta_bar_label: label || ''
            });
        }
        if (cfg.meta && cfg.meta.enabled && typeof fbq === 'function') {
            fbq('trackCustom', cfg.meta.eventName, { action: action, label: label || '' });
        }
    }

    function getLinkTarget(e) {
        var el = e.target;
        if (el.closest) return el.closest('a');
        while (el && el !== panel) {
            if (el.tagName === 'A') return el;
            el = el.parentNode;
        }
        return null;
    }

    if (closeOnLinkClick) {
        panel.addEventListener('click', function (e) {
            var link = getLinkTarget(e);
            if (link) {
                track('link_click', link.textContent.trim());
                close();
            }
        });
    } else {
        panel.addEventListener('click', function (e) {
            var link = getLinkTarget(e);
            if (link) {
                track('link_click', link.textContent.trim());
            }
        });
    }

    function announce(msg) {
        var el = document.getElementById('fpctabar-announcer');
        if (el) {
            el.textContent = '';
            setTimeout(function () {
                el.textContent = msg;
            }, 50);
        }
    }

    function toggle() {
        isOpen = !isOpen;
        container.classList.toggle('fpctabar--open', isOpen);
        trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        panel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        announce(isOpen ? (container.getAttribute('data-aria-open') || 'Pannello aperto') : (container.getAttribute('data-aria-closed') || 'Pannello chiuso'));
    }

    function close() {
        if (!isOpen) return;
        isOpen = false;
        container.classList.remove('fpctabar--open');
        trigger.setAttribute('aria-expanded', 'false');
        panel.setAttribute('aria-hidden', 'true');
        announce(container.getAttribute('data-aria-closed') || 'Pannello chiuso');
        if (dismissHours > 0) {
            try {
                localStorage.setItem('fpctabar_dismissed', String(Date.now()));
            } catch (e) {}
        }
    }

    trigger.addEventListener('click', function (e) {
        e.stopPropagation();
        track('bar_click');
        toggle();
    });

    trigger.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            toggle();
        }
    });

    document.addEventListener('click', function (e) {
        if (!container.contains(e.target)) {
            close();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            close();
        }
    });
})();
