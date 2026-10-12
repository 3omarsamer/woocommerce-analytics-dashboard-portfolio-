(()=>{
  'use strict';
  const cfg=window.AzulaBeginCheckout||{};
  if(!cfg.episode)return;
  const key='azula_begin_checkout_'+cfg.episode;
  const already=()=>{try{return sessionStorage.getItem(key)==='1'}catch(e){return false}};
  const mark=()=>{try{sessionStorage.setItem(key,'1')}catch(e){}};
  const usable=()=>!!document.querySelector('form.checkout, form.woocommerce-checkout');
  const dispatch=()=>{
    if(already()||!usable())return false;
    if(typeof window.gtag!=='function')return false;
    try{window.gtag('event','begin_checkout');mark();return true}catch(e){return false}
  };
  const start=()=>{
    if(dispatch())return;
    let tries=0;
    const timer=setInterval(()=>{tries++;if(dispatch()||tries>=20)clearInterval(timer)},250);
  };
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',start,{once:true});else start();
})();