/* Training Academy hero: animated VFD frequency display */
(function(){const el=document.getElementById('tr-vfd-display');if(!el)return;const values=[32.0,36.5,40.0,44.5,45.0,48.5,42.0,38.5];let i=0;const render=()=>{const f=values[i%values.length];el.textContent=f.toFixed(1)+' Hz';i++;};render();setInterval(render,1100);})();

/* Training Academy hero: scale the animated dashboard iframe (designed at 1250px wide) to the panel width */
(function(){const frame=document.querySelector('.hero-dashboard-frame');if(!frame)return;function size(){frame.style.setProperty('--dashboard-scale',(frame.clientWidth/1250).toFixed(5));}size();window.addEventListener('resize',size,{passive:true});})();
