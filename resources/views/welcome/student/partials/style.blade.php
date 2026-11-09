{{-- Shared look of the student profile and edit pages --}}
<style>
    .sp-wrap { padding: 34px 0 64px; background: radial-gradient(circle at 90% 0%, #e3f0ff, transparent 34%), radial-gradient(circle at 0% 100%, #e9faf0, transparent 30%), #f6f9fc; }
    .sp-wrap .wrap { max-width: 1100px; }

    /* Profile banner */
    .sp-hero { position: relative; background: #fff; border: 1px solid #e4eaf2; border-radius: 26px; overflow: hidden; box-shadow: 0 18px 40px rgba(17, 32, 60, .07); }
    .sp-cover { height: 128px; background: linear-gradient(110deg, #2eb454 0%, #22a3a0 30%, #287dcc 62%, #7b3fb8 100%); position: relative; }
    .sp-cover::after { content: ""; position: absolute; inset: 0; background-image: radial-gradient(rgba(255,255,255,.16) 1.2px, transparent 1.3px); background-size: 18px 18px; }
    .sp-hero-body { display: flex; align-items: flex-start; gap: 20px; padding: 0 28px 24px; position: relative; flex-wrap: wrap; }
    .sp-hero-body > .sp-photo { margin-top: -58px; }
    .sp-photo { width: 116px; height: 116px; border-radius: 28px; border: 5px solid #fff; background: linear-gradient(135deg, #2eb454, #287dcc); box-shadow: 0 12px 28px rgba(17, 32, 60, .16); display: grid; place-items: center; color: #fff; font-size: 38px; font-weight: 800; overflow: hidden; flex: 0 0 116px; }
    .sp-photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .sp-id { flex: 1 1 280px; min-width: 0; padding-top: 16px; }
    .sp-id h1 { font-size: 26px; margin: 0 0 6px; color: #172338; letter-spacing: -.5px; word-break: break-word; }
    .sp-reg { display: inline-flex; align-items: center; gap: 7px; padding: 5px 12px; border-radius: 999px; background: #eef5ff; color: #1e6fc4; font-weight: 800; font-size: 12px; letter-spacing: .4px; }
    .sp-chips { display: flex; flex-wrap: wrap; gap: 8px 16px; margin-top: 10px; color: #5d6f86; font-size: 13px; }
    .sp-chips span { display: inline-flex; align-items: center; gap: 7px; word-break: break-all; }
    .sp-chips i { color: #287dcc; }
    .sp-actions { display: flex; gap: 10px; padding-top: 18px; flex-wrap: wrap; }
    .sp-btn { display: inline-flex; align-items: center; gap: 8px; border-radius: 12px; padding: 11px 18px; font-weight: 700; font-size: 13px; border: 1px solid transparent; cursor: pointer; text-decoration: none; transition: .2s; line-height: 1.2; }
    .sp-btn-primary { background: linear-gradient(135deg, #287dcc, #1e64ad); color: #fff; box-shadow: 0 10px 22px rgba(40, 125, 204, .25); }
    .sp-btn-primary:hover { color: #fff; transform: translateY(-2px); }
    .sp-btn-light { background: #fff; color: #22466d; border-color: #dce5ef; }
    .sp-btn-light:hover { background: #f3f7fc; color: #22466d; }
    .sp-btn-danger { background: #fff; color: #d22d4b; border-color: #f3c9d2; }
    .sp-btn-danger:hover { background: #fff3f5; }

    /* Stat cards */
    .sp-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin: 20px 0 24px; }
    .sp-stat { display: flex; align-items: center; gap: 14px; background: #fff; border: 1px solid #e4eaf2; border-radius: 20px; padding: 18px; box-shadow: 0 10px 26px rgba(17, 32, 60, .04); }
    .sp-stat .ic { width: 46px; height: 46px; border-radius: 14px; display: grid; place-items: center; font-size: 18px; flex: 0 0 46px; }
    .sp-stat small { display: block; color: #6b7c92; font-size: 12px; font-weight: 700; }
    .sp-stat b { display: block; font-size: 22px; color: #172338; line-height: 1.2; }

    /* Sections */
    .sp-section { background: #fff; border: 1px solid #e4eaf2; border-radius: 24px; padding: 24px; margin-bottom: 22px; box-shadow: 0 12px 30px rgba(17, 32, 60, .04); }
    .sp-section-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
    .sp-section h2 { font-size: 19px; margin: 0; color: #172338; display: flex; align-items: center; gap: 10px; }
    .sp-section h2 i { color: #287dcc; }
    .sp-count { font-size: 12px; font-weight: 800; color: #64748b; background: #f1f5f9; border-radius: 999px; padding: 4px 11px; }
    .sp-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }

    .sp-course { border: 1px solid #e8eef5; border-radius: 18px; padding: 18px; background: linear-gradient(180deg, #fff, #fafcff); position: relative; overflow: hidden; }
    .sp-course::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(180deg, #2eb454, #287dcc); }
    .sp-course h3 { font-size: 16px; margin: 0 0 10px; color: #1c2b40; line-height: 1.4; }
    .sp-meta { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }
    .sp-tag { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: #4a5d75; background: #f1f5fa; border-radius: 999px; padding: 4px 10px; }
    .sp-money { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 10px; }
    .sp-money div { background: #f7f9fc; border-radius: 12px; padding: 8px 10px; }
    .sp-money small { display: block; font-size: 11px; color: #6b7c92; font-weight: 700; }
    .sp-money b { font-size: 14px; color: #172338; }
    .sp-money .due b { color: #d22d4b; }
    .sp-money .ok b { color: #1f8a46; }
    .sp-bar { height: 8px; border-radius: 999px; background: #e9eef5; overflow: hidden; }
    .sp-bar span { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, #2eb454, #287dcc); }
    .sp-bar-label { display: flex; justify-content: space-between; font-size: 11px; color: #6b7c92; font-weight: 700; margin-top: 6px; }

    .sp-cert { display: flex; align-items: center; gap: 14px; border: 1px solid #e8eef5; border-radius: 18px; padding: 16px; background: #fff; }
    .sp-cert .seal { width: 52px; height: 52px; border-radius: 16px; display: grid; place-items: center; background: linear-gradient(135deg, #fff4dc, #ffe2a8); color: #c98a06; font-size: 22px; flex: 0 0 52px; }
    .sp-cert-info { flex: 1; min-width: 0; }
    .sp-cert-info b { display: block; color: #1c2b40; font-size: 15px; line-height: 1.35; }
    .sp-cert-info span { display: block; color: #6b7c92; font-size: 12px; margin-top: 3px; }
    .sp-cert-info .sp-grade { display: inline-block; margin-top: 0; padding: 2px 9px; border-radius: 999px; background: #e8f8ee; color: #1f8a46; font-weight: 800; font-size: 12px; margin-left: 4px; }

    .sp-cert-actions { display: flex; flex-direction: column; gap: 8px; }
    .sp-cert-actions .sp-btn { justify-content: center; padding: 9px 14px; }
    .sp-empty { display: flex; align-items: center; gap: 14px; color: #5d6f86; padding: 18px; border-radius: 16px; background: #f6f9fd; border: 1px dashed #d3e0ee; }
    .sp-empty i { font-size: 26px; color: #9fb4cc; }

    .sp-alert { display: flex; align-items: center; gap: 10px; border-radius: 14px; padding: 12px 16px; margin-bottom: 16px; font-weight: 600; }
    .sp-alert.ok { background: #e8f8ee; color: #1f6b3b; border: 1px solid #c9ecd6; }
    .sp-alert.err { background: #fff1f3; color: #b4233f; border: 1px solid #f6cbd4; }
    .sp-alert ul { margin: 4px 0 0; padding-left: 18px; font-weight: 500; }

    /* Edit forms */
    .sp-form-grid { display: grid; grid-template-columns: 1.35fr 1fr; gap: 22px; align-items: start; }
    .sp-field { margin-bottom: 16px; }
    .sp-field label { display: block; font-weight: 700; font-size: 13px; color: #273750; margin-bottom: 7px; }
    .sp-field label .req { color: #d22d4b; }
    .sp-field input[type=text], .sp-field input[type=email], .sp-field input[type=tel], .sp-field input[type=password] { width: 100%; border: 1px solid #dbe4ee; border-radius: 12px; padding: 12px 14px; font-size: 14px; background: #fbfcfe; transition: .2s; }
    .sp-field input:focus { outline: none; border-color: #287dcc; background: #fff; box-shadow: 0 0 0 4px rgba(40, 125, 204, .12); }
    .sp-field .hint { font-size: 12px; color: #6b7c92; margin-top: 6px; }
    .sp-field .err { font-size: 12px; color: #d22d4b; margin-top: 6px; font-weight: 600; }
    .sp-field.has-err input { border-color: #e88a9c; }
    .sp-row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .sp-photo-edit { display: flex; align-items: center; gap: 18px; padding: 16px; border: 1px dashed #cfdceb; border-radius: 18px; background: #f8fbff; margin-bottom: 18px; flex-wrap: wrap; }
    .sp-photo-edit .sp-photo { width: 92px; height: 92px; flex-basis: 92px; border-width: 4px; font-size: 30px; border-radius: 24px; }
    .sp-photo-edit input[type=file] { font-size: 13px; max-width: 100%; color: #5d6f86; }
    .sp-photo-edit input[type=file]::file-selector-button { margin-right: 10px; border: 1px solid #cfdceb; background: #fff; color: #1e64ad; font-weight: 700; border-radius: 10px; padding: 8px 14px; cursor: pointer; }
    .sp-photo-edit input[type=file]::file-selector-button:hover { background: #eef5ff; }

    @media (max-width: 900px) { .sp-stats { grid-template-columns: repeat(2, 1fr); } .sp-grid, .sp-form-grid { grid-template-columns: 1fr; } }
    @media (max-width: 560px) { .sp-hero-body { padding: 0 18px 20px; } .sp-id, .sp-actions { padding-top: 0; } .sp-cert { flex-wrap: wrap; } .sp-cert-actions { flex-direction: row; width: 100%; } .sp-cert-actions .sp-btn { flex: 1; } .sp-stats { grid-template-columns: 1fr 1fr; gap: 10px; } .sp-stat { padding: 14px; } .sp-stat b { font-size: 18px; } .sp-section { padding: 18px; } .sp-money { grid-template-columns: 1fr 1fr 1fr; } .sp-row2 { grid-template-columns: 1fr; } .sp-id h1 { font-size: 22px; } }
</style>
