document.addEventListener("click", (e)=>{
  const a = e.target.closest('a[href^="#"]');
  if(!a) return;
  e.preventDefault();
  const el = document.querySelector(a.getAttribute("href"));
  if(el) el.scrollIntoView({behavior:"smooth", block:"start"});
});
