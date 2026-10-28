/* Training Academy hero: animated VFD frequency display */
(function(){const el=document.getElementById('tr-vfd-display');if(!el)return;const values=[32.0,36.5,40.0,44.5,45.0,48.5,42.0,38.5];let i=0;const render=()=>{const f=values[i%values.length];el.textContent=f.toFixed(1)+' Hz';i++;};render();setInterval(render,1100);})();
