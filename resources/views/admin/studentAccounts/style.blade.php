{{-- Registered Students list and student page --}}
<style>
.rs-card{background:#fff;border:1px solid #e3eaf2;border-radius:14px;overflow:hidden}
.rs-top{display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid #edf1f6}
.rs-filters{display:flex;flex-wrap:wrap;gap:6px}
.rs-filters a{padding:5px 11px;border-radius:999px;border:1px solid #dbe4ee;color:#40536b;font-size:12px;font-weight:700;text-decoration:none;background:#fff}
.rs-filters a b{font-weight:800;margin-left:3px}
.rs-filters a.on{background:#287dcc;border-color:#287dcc;color:#fff}
.rs-filters a.wait{border-color:#f3c4c8;color:#c4313b}
.rs-filters a.wait.on{background:#e5484d;border-color:#e5484d;color:#fff}
.rs-search{display:flex;gap:6px}
.rs-search input{height:32px;border:1px solid #dbe4ee;border-radius:8px;padding:0 10px;font-size:13px;width:210px;max-width:52vw}
.rs-search button{height:32px;width:34px;border:1px solid #dbe4ee;border-radius:8px;background:#fff;color:#40536b}
.rs-note{margin:0;padding:9px 14px;font-size:12px;color:#6b7c92;background:#f8fbff;border-bottom:1px solid #edf1f6}
.rs-table{width:100%;border-collapse:collapse;font-size:13px}
.rs-table th{font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#7a8aa0;font-weight:800;padding:9px 12px;text-align:left;background:#fbfcfe;border-bottom:1px solid #edf1f6;white-space:nowrap}
.rs-table td{padding:8px 12px;border-bottom:1px solid #f0f3f7;vertical-align:middle}
.rs-table tr.rs-row:hover td{background:#fafcff}
.rs-table tr.wait td{background:#fff9f9}
.rs-who{display:flex;align-items:center;gap:10px;min-width:0}
.rs-avatar{width:36px;height:36px;border-radius:50%;flex:0 0 36px;display:grid;place-items:center;font-weight:800;font-size:13px;color:#fff;background:linear-gradient(135deg,#287dcc,#5a4fcf);overflow:hidden}
.rs-avatar img{width:100%;height:100%;object-fit:cover}
.rs-name{font-weight:800;color:#172338;line-height:1.2}
.rs-sub{font-size:11px;color:#8494a8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:230px}
.rs-course{font-size:12px;color:#33506f;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.rs-chip{display:inline-block;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:800;white-space:nowrap}
.rs-chip.wait{background:#fdecee;color:#c4313b}
.rs-chip.ok{background:#e6f7ee;color:#1f8a46}
.rs-chip.off{background:#f1f3f6;color:#6b7c92}
.rs-chip.id{background:#eaf3ff;color:#1e6fc4}
.rs-date{font-size:12px;color:#40536b;white-space:nowrap}
.rs-date small{display:block;color:#9aa8b8;font-size:11px}
.rs-acts{display:flex;gap:5px;justify-content:flex-end}
.rs-acts form{margin:0}
.rs-ic{width:30px;height:30px;border-radius:8px;display:inline-grid;place-items:center;border:1px solid #dfe6ee;background:#fff;color:#55677e;font-size:13px;cursor:pointer;padding:0;transition:.15s}
.rs-ic:hover{transform:translateY(-1px)}
.rs-ic.view:hover,.rs-ic.view.on{background:#eaf3ff;border-color:#b9d5f3;color:#1e6fc4}
.rs-ic.approve{background:#1f9d55;border-color:#1f9d55;color:#fff}
.rs-ic.approve:hover{background:#178246}
.rs-ic.reject:hover,.rs-ic.del:hover{background:#fdecee;border-color:#f3c4c8;color:#c4313b}
.rs-ic.block:hover{background:#fff6e5;border-color:#f3d79b;color:#a46a00}
.rs-ic.unblock:hover{background:#e6f7ee;border-color:#a8dcbd;color:#1f8a46}
.rs-detail td{background:#f8fbff!important;padding:12px 14px 14px}
.rs-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:8px}
.rs-grid div{background:#fff;border:1px solid #e6edf5;border-radius:9px;padding:7px 10px;min-width:0}
.rs-grid small{display:block;font-size:10px;color:#8494a8;font-weight:800;text-transform:uppercase;letter-spacing:.4px}
.rs-grid b{font-size:12px;color:#172338;overflow-wrap:anywhere}
.rs-grid b.none{color:#a5b2c2;font-weight:600;font-style:italic}
.rs-recs{margin-top:10px}
.rs-recs h6{font-size:11px;font-weight:800;color:#7a8aa0;text-transform:uppercase;letter-spacing:.5px;margin:0 0 6px}
.rs-rec{display:flex;align-items:center;gap:8px;padding:5px 0;font-size:12px;color:#33506f}
.rs-rec form{margin:0}
.rs-rec .rs-mini{border:1px solid #dfe6ee;background:#fff;border-radius:6px;font-size:11px;font-weight:800;padding:2px 8px;cursor:pointer}
.rs-rec .rs-mini.link{background:#1f9d55;border-color:#1f9d55;color:#fff}
.rs-rec .rs-mini.unlink:hover{background:#fdecee;color:#c4313b}
.rs-empty{padding:26px;text-align:center;color:#9aa8b8}
@media(max-width:600px){.rs-table td,.rs-table th{padding:8px 6px}.rs-sub{max-width:120px}.rs-name{font-size:12px}.rs-avatar{width:30px;height:30px;flex-basis:30px;font-size:11px}.rs-acts{gap:3px}.rs-ic{width:28px;height:28px}.rs-chip.id{display:none}}
/* List: fits the page (no side scroll); columns drop out on narrower screens */
.rs-wrap{overflow:hidden}
.rs-list{table-layout:fixed}
.rs-list td{padding:7px 10px}
.rs-list th{padding:8px 10px}
.rs-list th.c-mob{width:125px}.rs-list th.c-course{width:24%}.rs-list th.c-status{width:86px}.rs-list th.c-date{width:96px}.rs-list th.c-act{width:112px}
.rs-list .rs-sub{max-width:100%}
.rs-list .rs-course{max-width:100%}
.rs-cut{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
a.rs-ic{text-decoration:none}
@media(max-width:1350px){.rs-list .c-date{display:none}}
@media(max-width:1150px){.rs-list .c-mob{display:none}}
@media(max-width:950px){.rs-list .c-course{display:none}}
@media(max-width:600px){.rs-list th.c-act{width:100px}.rs-list th.c-status{width:72px}}
</style>
