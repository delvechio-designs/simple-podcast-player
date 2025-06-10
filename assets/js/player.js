document.addEventListener('DOMContentLoaded',()=>{
  document.querySelectorAll('.spp-card').forEach(card=>{
    const url   = card.dataset.audio;
    const play  = card.querySelector('.spp-play');
    const waveC = card.querySelector('.spp-wave');
    const time  = card.querySelector('.spp-time');
    const speed = card.querySelector('.spp-speed');

    const ws = WaveSurfer.create({
      container:waveC,
      waveColor:getComputedStyle(document.documentElement).getPropertyValue('--spp-wave'),
      progressColor:getComputedStyle(document.documentElement).getPropertyValue('--spp-progress'),
      barWidth:2,height:70,responsive:true
    });
    ws.load(url);

    const fmt=s=>`${String(Math.floor(s/60)).padStart(2,'0')}:${String(Math.floor(s%60)).padStart(2,'0')}`;
    play.onclick=()=>{ws.playPause();play.textContent=ws.isPlaying()?'❚❚':'▶';};
    card.querySelectorAll('[data-skip]').forEach(btn=>btn.onclick=()=>ws.setCurrentTime(ws.getCurrentTime()+parseFloat(btn.dataset.skip)));
    speed.onclick=()=>{const n=ws.getPlaybackRate()===1?1.5:ws.getPlaybackRate()===1.5?2:1;ws.setPlaybackRate(n);speed.textContent=n+'×';};

    ws.on('ready', ()=>{time.textContent=`00:00 | ${fmt(ws.getDuration())}`; ws.drawBuffer();});
    ws.on('audioprocess',()=>{time.textContent=`${fmt(ws.getCurrentTime())} | ${fmt(ws.getDuration())}`});

    if(window.SPP_DATA&&typeof gtag==='function'){
      const send=e=>gtag('event',e,{event_category:'podcast',event_label:url});
      ws.on('play', ()=>send('play'));
      ws.on('finish',()=>send('complete'));
      card.querySelector('.spp-download').onclick=()=>send('download');
    }
  });
});
