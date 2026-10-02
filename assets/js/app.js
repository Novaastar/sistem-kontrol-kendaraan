document.addEventListener('DOMContentLoaded',()=> {
  document.querySelectorAll('[data-uppercase]').forEach(el=>{
    el.addEventListener('input',()=>el.value=el.value.toUpperCase());
  });
  document.querySelectorAll('[data-confirm]').forEach(el=>{
    el.addEventListener('click',e=>{
      if(!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });
});
