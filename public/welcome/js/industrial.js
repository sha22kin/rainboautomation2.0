/* Industrial Solution hero: animated VFD frequency display */
(function(){
  const freqEl = document.getElementById('ra-vfd-frequency-display');
  const motorEl = document.getElementById('ra-motor-speed-display');
  if(!freqEl) return;
  const values = [32.0, 36.5, 40.0, 44.5, 48.5, 46.0, 42.0, 38.5];
  let i = 0;
  const render = () => {
    const f = values[i % values.length];
    const rpm = Math.round(f * 30);
    freqEl.textContent = f.toFixed(1) + ' Hz';
    if(motorEl){
      motorEl.innerHTML = '<span class="hz">' + f.toFixed(1) + ' Hz</span><span class="rpm">' + rpm + ' RPM</span>';
    }
    i++;
  };
  render();
  setInterval(render, 1100);
})();
