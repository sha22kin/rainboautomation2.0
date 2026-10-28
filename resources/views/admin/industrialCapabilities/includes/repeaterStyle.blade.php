{{-- Card-style editor for repeatable items (capability / solution cards, hero chips, workflow steps).
     Uses the nx-admin theme variables so it follows light and dark mode. --}}
<style>
    .nx-rep-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 16px; }
    .nx-rep-grid.is-compact { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); }
    .nx-rep-item {
        --rep-accent: var(--nx-primary, #0b5fa5);
        position: relative; display: flex; flex-direction: column; overflow: hidden;
        background: var(--nx-card, #fff); border: 1px solid var(--nx-line, #e6ecf3);
        border-radius: var(--nx-radius, 14px); box-shadow: var(--nx-shadow, none);
        transition: box-shadow .2s, transform .2s, border-color .2s;
    }
    .nx-rep-item::before { content: ""; height: 4px; background: var(--rep-accent); flex: 0 0 4px; }
    .nx-rep-item:hover { box-shadow: var(--nx-shadow-lg, 0 10px 30px rgba(16, 38, 74, .12)); border-color: color-mix(in srgb, var(--rep-accent) 35%, var(--nx-line, #e6ecf3)); }
    .nx-rep-item.is-new { animation: nxRepIn .35s var(--nx-ease, ease); }
    @keyframes nxRepIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }

    .nx-rep-head { display: flex; align-items: center; gap: 8px; padding: 10px 12px; border-bottom: 1px solid var(--nx-line, #e6ecf3); }
    .nx-rep-num {
        display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 26px; padding: 0 8px;
        border-radius: 999px; font-size: 12px; font-weight: 800; color: var(--rep-accent);
        background: color-mix(in srgb, var(--rep-accent) 13%, transparent);
    }
    .nx-rep-kind { font-size: 11px; font-weight: 700; letter-spacing: .6px; text-transform: uppercase; color: var(--nx-muted, #7b8a9e); }
    .nx-rep-actions { margin-left: auto; display: flex; gap: 4px; }
    .nx-rep-btn {
        width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; padding: 0;
        border: 1px solid transparent; border-radius: var(--nx-radius-sm, 10px); background: transparent;
        color: var(--nx-muted, #7b8a9e); font-size: 12px; cursor: pointer; transition: background .15s, color .15s, border-color .15s;
    }
    .nx-rep-btn:hover { background: var(--nx-primary-50, #eaf2fb); color: var(--nx-primary, #0b5fa5); }
    .nx-rep-btn.is-danger:hover { background: var(--nx-red-50, #fde8ec); color: var(--nx-red, #e4002b); }
    .nx-rep-btn:focus-visible { outline: 2px solid var(--nx-primary, #0b5fa5); outline-offset: 1px; }

    .nx-rep-body { padding: 14px; display: flex; flex-direction: column; gap: 12px; flex: 1; }
    .nx-rep-body label { font-size: 12px; font-weight: 600; color: var(--nx-muted, #7b8a9e); margin-bottom: 4px; }
    .nx-rep-body .form-group { margin: 0; }
    .nx-rep-top { display: flex; gap: 12px; align-items: flex-end; }
    .nx-rep-top .form-control:not(.nx-rep-icon) { height: 54px; }
    .nx-rep-top .form-group { flex: 1; min-width: 0; }
    .nx-rep-icon {
        width: 54px !important; height: 54px !important; min-height: 54px; flex: 0 0 54px; padding: 0 !important;
        text-align: center; font-size: 26px !important; line-height: 54px !important;
        border-radius: 16px; border: 1px dashed color-mix(in srgb, var(--rep-accent) 45%, transparent) !important;
        background: color-mix(in srgb, var(--rep-accent) 12%, var(--nx-card, #fff)) !important; color: var(--nx-ink, #111c2d);
    }
    .nx-rep-icon:focus { border-style: solid !important; }
    .nx-rep-title { font-weight: 700; color: var(--nx-ink, #111c2d) !important; }
    .nx-rep-body textarea { resize: vertical; min-height: 64px; }

    .nx-rep-color { display: flex; align-items: center; gap: 10px; margin-top: auto; }
    .nx-rep-color label { margin: 0; }
    .nx-rep-color input[type=color] {
        width: 34px; height: 34px; padding: 0; border: 1px solid var(--nx-line, #e6ecf3); border-radius: 50%;
        background: none; cursor: pointer; overflow: hidden;
    }
    .nx-rep-color input[type=color]::-webkit-color-swatch-wrapper { padding: 0; }
    .nx-rep-color input[type=color]::-webkit-color-swatch { border: 0; border-radius: 50%; }
    .nx-rep-color input[type=color]::-moz-color-swatch { border: 0; border-radius: 50%; }
    .nx-rep-hex { font-size: 12px; font-family: monospace; color: var(--nx-muted, #7b8a9e); text-transform: uppercase; }

    .nx-rep-empty {
        display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 28px 16px; text-align: center;
        border: 1px dashed var(--nx-line, #e6ecf3); border-radius: var(--nx-radius, 14px); color: var(--nx-muted, #7b8a9e); margin: 0;
    }
    .nx-rep-empty i { font-size: 22px; opacity: .6; }
    .nx-rep-section { margin-bottom: 18px; padding-bottom: 18px; border-bottom: 1px solid var(--nx-line, #e6ecf3); }

    /* Card list table (Industrial Automation Capabilities / Solar Power Solutions) */
    .nx-card-table td { vertical-align: middle; }
    .nx-card-table .orderBtns a { display: inline-block; padding: 2px 7px; }
    .nx-card-table td:last-child { white-space: nowrap; }
    .nx-card-icon {
        --rep-accent: var(--nx-primary, #0b5fa5);
        display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; border-radius: 12px; font-size: 21px;
        background: color-mix(in srgb, var(--rep-accent) 14%, var(--nx-card, #fff)); box-shadow: inset 0 -3px 0 var(--rep-accent);
    }
    .nx-card-desc { color: var(--nx-muted, #7b8a9e); font-size: 12px; }
    .nx-card-swatch { display: inline-block; width: 18px; height: 18px; border-radius: 50%; vertical-align: middle; border: 1px solid var(--nx-line, #e6ecf3); }
    .nx-chip-preview { display: inline-flex; align-items: flex-start; gap: 10px; }
    .nx-chip-preview .nx-card-swatch { width: 12px; height: 12px; margin-top: 4px; border: 0; box-shadow: 0 0 0 5px rgba(38, 132, 217, .08); }
    .nx-chip-preview small { display: block; color: var(--nx-muted, #7b8a9e); font-size: 12px; }
    .nx-step-no {
        display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 30px; padding: 0 8px; border-radius: 999px;
        font-weight: 800; font-size: 13px; color: var(--nx-primary, #0b5fa5); background: var(--nx-primary-50, #eaf2fb);
    }
</style>
