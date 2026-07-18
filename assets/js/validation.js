function isEmail(v){return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);}
document.addEventListener("submit",(e)=>{
  const f = e.target;
  if(f.matches("[data-validate]")){
    const email = f.querySelector('input[type="email"]');
    if(email && !isEmail(email.value.trim())){
      e.preventDefault();
      alert("Valid email daalo.");
    }
  }
}, true);
