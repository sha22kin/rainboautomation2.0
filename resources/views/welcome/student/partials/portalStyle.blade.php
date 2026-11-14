{{-- Student portal look (design: desing/Student_Portal.html), used by the home page section and the student login / register pages --}}
<style>
.sp{background:#f2f7fb;padding:66px 0}
.sp-head{text-align:center}
.sp-eyebrow{font-size:10px;text-transform:uppercase;font-weight:850;letter-spacing:1.7px;color:#0787a4}
.sp-head h2,.sp-head .sp-title{font-size:clamp(27px,3vw,36px);line-height:1.16;letter-spacing:-1px;margin:10px auto 12px;max-width:820px;font-weight:800;color:#102333}
.sp-lead{font-size:13px;color:#63788b;max-width:690px;margin:0 auto 24px}
.sp-tabs{display:flex;flex-wrap:wrap;gap:11px;margin-bottom:18px}
.sp-tab{padding:13px 19px;border-radius:9px;border:1px solid #dbe6ef;color:#274258;background:#fff;font:inherit;font-size:12px;font-weight:800;cursor:pointer;text-decoration:none;display:inline-block}
.sp-tab.active{background:#0d7fc1;color:#fff;border-color:#0d7fc1;box-shadow:0 8px 17px #0d7fc126}
.sp-shell{background:#fff;border:1px solid #e1eaf3;border-radius:20px;overflow:hidden;display:grid;grid-template-columns:310px 1fr;box-shadow:0 10px 34px rgba(22,56,90,.09)}
.sp-info{background:linear-gradient(165deg,#124668,#102d4b 68%,#10243c);color:#fff;padding:33px}
.sp-info .sp-eyebrow{color:#5ae0e9}
.sp-info h3{font-size:26px;line-height:1.17;margin:16px 0;color:#fff;font-weight:800}
.sp-info p{font-size:12px;color:#bed1e2}
.sp-info ul{list-style:none;padding:7px 0;margin:0;font-size:12px;line-height:2.5}
.sp-info li::before{content:'✓';color:#4ce0bf;margin-right:8px}
.sp-panel{padding:30px 35px;min-height:390px;text-align:left}
.sp-panel[hidden]{display:none}
.sp-panel h3,.sp-panel .sp-panel-title{font-size:23px;margin:0 0 3px;font-weight:800;color:#102333}
.sp-panel>p{font-size:12px;color:#63788b;margin:0 0 22px}
.sp-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}
.sp-login{max-width:460px;grid-template-columns:1fr}
.sp-field{display:flex;flex-direction:column;gap:6px}
.sp-field.full{grid-column:1/-1}
.sp-field label{font-size:11px;font-weight:750;color:#102333}
.sp-field label b{color:#d94646}
.sp-field input,.sp-field select{width:100%;height:43px;border-radius:9px;border:1px solid #d9e4ec;background:#fbfdff;padding:0 12px;font:inherit;font-size:12px;color:#223d52;outline:0;box-sizing:border-box}
.sp-field input:focus,.sp-field select:focus{border-color:#0f91cb;box-shadow:0 0 0 3px #0a89bd1a}
.sp-pass{display:flex;gap:5px}
.sp-foot{display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-top:20px}
.sp-btn{border:0;border-radius:30px;padding:11px 18px;background:#0789ca;color:#fff;font:inherit;font-size:12px;font-weight:750;display:inline-flex;gap:8px;justify-content:center;align-items:center;cursor:pointer;transition:.2s;text-decoration:none}
.sp-btn:hover{background:#006ca8;color:#fff;transform:translateY(-1px)}
.sp-btn.outline{color:#103b5c;background:#fff;border:1px solid #d7e2ea}
.sp-btn.outline:hover{background:#e9f5ff;color:#103b5c}
.sp-btn.green{background:#178e6a}
.sp-btn.green:hover{background:#127a5a}
.sp-check{display:flex;align-items:center;gap:7px;color:#63788b;font-size:11px}
.sp-check input{accent-color:#0b8cc2}
.sp-link{background:none;border:0;color:#087fbd;font-size:11px;font-weight:800;padding:0;text-decoration:none}
.sp-notice{font-size:11px;background:#edf9ff;border:1px solid #d3effd;color:#416b83;padding:12px;border-radius:9px;margin-top:17px}
.sp-alert{margin:0 0 16px;padding:12px 13px;border-radius:9px;font-size:12px;background:#fff1f1;border:1px solid #f3c2c2;color:#9b2c2c}
.sp-alert.good{background:#eefaf2;border-color:#a6e0bb;color:#1d6b3e}
.sp-wrap{width:min(1170px,calc(100% - 32px));margin:auto}
.sp-field input.bad,.sp-field select.bad{border-color:#e08a8a;background:#fffafa}
.sp-err{font-size:11px;color:#b83232}
.sp-tab:hover{border-color:#0d7fc1;color:#0d7fc1}
.sp-tab.active:hover{color:#fff}
.sp-alert ul{margin:6px 0 0;padding-left:18px}
.sp-hp{position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden}
.sp-steps{background:#eaf3f9;padding:65px 0;text-align:center}
.sp-steps h2{font-size:clamp(27px,3vw,39px);line-height:1.16;letter-spacing:-1.2px;margin:10px auto 30px;max-width:820px}
.sp-stepgrid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
.sp-step{background:#fff;border-radius:15px;padding:26px;min-height:160px;box-shadow:0 10px 34px rgba(22,56,90,.09);text-align:left}
.sp-step h3{font-size:17px;margin:10px 0 8px}
.sp-step p{font-size:12px;color:#63788b;margin:0}
@media(max-width:970px){.sp-shell{grid-template-columns:250px 1fr}.sp-info{padding:24px}}
@media(max-width:760px){.sp-shell{grid-template-columns:1fr}.sp-info{padding:25px}.sp-info ul{display:grid;grid-template-columns:1fr 1fr;gap:0 10px}.sp-info h3{font-size:22px}.sp-panel{padding:26px 22px;min-height:0}.sp-stepgrid{grid-template-columns:1fr}}
@media(max-width:500px){.sp-fields{grid-template-columns:1fr}.sp-info ul{grid-template-columns:1fr}.sp-tabs{display:grid;grid-template-columns:1fr 1fr}.sp-tab:last-child{grid-column:1/-1}.sp,.sp-steps{padding:46px 0}}
</style>
