// app.js - simple frontend interactions + demo storage
const demoTrainers = [
  {id:1,name:"John Smith",title:"Strength Coach & Powerlifting Specialist",img:"images/trainer1.jpg",bio:"Focus on strength, squats, deadlifts and peaking."},
  {id:2,name:"Sarah Lee",title:"Certified Nutritionist & Fitness Coach",img:"images/trainer2.jpg",bio:"Smart nutrition coaching for sustainable results."},
  {id:3,name:"David Kim",title:"CrossFit & HIIT Instructor",img:"images/trainer3.jpg",bio:"High-energy conditioning and technique."}
];

const testimonials = [
  {text:'"Strength House changed my life! The trainers are amazing and the atmosphere is motivating." – Emma R.'},
  {text:'"Best gym in Sydney! Great equipment and fantastic group classes." – Mark T.'},
  {text:'"I improved my squat by 30kg in six months thanks to the coaches." – Anna P.'}
];

// set year fields
document.querySelectorAll('[id^="year"]').forEach(el => el.textContent = new Date().getFullYear());

// mobile menu toggle
document.querySelectorAll('#mobile-toggle').forEach(btn=>{
  btn.addEventListener('click', ()=> {
    const nav = document.querySelector('.nav');
    if(nav) nav.style.display = nav.style.display === 'flex' ? 'none' : 'flex';
  });
});

// populate trainers previews on home and trainers page
function renderTrainersInto(containerId){
  const c = document.getElementById(containerId);
  if(!c) return;
  c.innerHTML = '';
  demoTrainers.forEach(t=>{
    const card = document.createElement('div');
    card.className = 'trainer-card';
    card.innerHTML = `<img src="${t.img}" alt="${t.name}"><h3>${t.name}</h3><p>${t.title}</p><p class="muted">${t.bio}</p><div class="center"><button class="btn-outline view-trainer" data-id="${t.id}">View</button></div>`;
    c.appendChild(card);
  });
}
renderTrainersInto('home-trainers');
renderTrainersInto('trainers-grid');

// trainer search (trainers page)
const searchInput = document.getElementById('trainer-search');
if(searchInput){
  searchInput.addEventListener('input', e=>{
    const q = e.target.value.toLowerCase();
    const grid = document.getElementById('trainers-grid');
    grid.innerHTML = '';
    demoTrainers.filter(t=> t.name.toLowerCase().includes(q) || t.title.toLowerCase().includes(q))
      .forEach(t=> {
        const card = document.createElement('div');
        card.className = 'trainer-card';
        card.innerHTML = `<img src="${t.img}" alt="${t.name}"><h3>${t.name}</h3><p>${t.title}</p><p class="muted">${t.bio}</p>`;
        grid.appendChild(card);
      });
  });
}

// testimonials carousel
let testIdx = 0;
function showTest(idx){
  const wrap = document.getElementById('testimonial-wrap');
  if(!wrap) return;
  wrap.textContent = testimonials[idx].text;
}
document.getElementById('prev-test')?.addEventListener('click', ()=>{
  testIdx = (testIdx-1+testimonials.length)%testimonials.length;
  showTest(testIdx);
});
document.getElementById('next-test')?.addEventListener('click', ()=>{
  testIdx = (testIdx+1)%testimonials.length;
  showTest(testIdx);
});
showTest(testIdx);

// Simple member "database" in localStorage
const MEMBER_KEY = 'sh_members_v1';
function getMembers(){ try{ return JSON.parse(localStorage.getItem(MEMBER_KEY) || '[]'); }catch(e){ return []; } }
function saveMember(member){
  const m = getMembers();
  m.push(member);
  localStorage.setItem(MEMBER_KEY, JSON.stringify(m));
}

// registration form
const regForm = document.getElementById('register-form');
if(regForm){
  // prefill plan from query string
  const params = new URLSearchParams(location.search);
  const planParam = params.get('plan');
  if(planParam){
    const planSelect = document.getElementById('plan');
    if(planSelect) planSelect.value = planParam;
  }

  regForm.addEventListener('submit', e=>{
    e.preventDefault();
    const name = document.getElementById('fullName').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const plan = document.getElementById('plan').value;
    const password = document.getElementById('password').value;
    const msg = document.getElementById('form-msg');

    if(!name || !email || password.length < 6){
      msg.textContent = 'Please complete required fields (password min 6 chars).';
      return;
    }

    const member = {id:Date.now(), name, email, phone, plan, created: new Date().toISOString()};
    saveMember(member);
    msg.textContent = `Thanks ${name}! Registered on plan: ${plan}. (Demo: stored locally)`;
    regForm.reset();
  });

  document.getElementById('list-members').addEventListener('click', ()=>{
    const listPanel = document.getElementById('members-list');
    const ul = document.getElementById('members-ul');
    ul.innerHTML = '';
    getMembers().forEach(m=>{
      const li = document.createElement('li');
      li.textContent = `${m.name} — ${m.email} — ${m.plan}`;
      ul.appendChild(li);
    });
    listPanel.classList.toggle('hidden');
  });
}

// contact form demo
const contactForm = document.getElementById('contact-form');
if(contactForm){
  contactForm.addEventListener('submit', e=>{
    e.preventDefault();
    const n = document.getElementById('contact-name').value.trim();
    const em = document.getElementById('contact-email').value.trim();
    const m = document.getElementById('contact-message').value.trim();
    const out = document.getElementById('contact-msg');
    if(!n || !em || !m){ out.textContent = 'Please fill all fields.'; return; }
    // demo: show message and log to console (replace with real API on server)
    out.textContent = 'Message sent (demo). We will respond soon.';
    console.log('Contact form message (demo):', {name:n,email:em,message:m});
    contactForm.reset();
  });
}

// nav active highlighting
document.querySelectorAll('.nav-link').forEach(link=>{
  if(location.pathname.endsWith(link.getAttribute('href')) || location.href.endsWith(link.getAttribute('href'))){
    link.classList.add('active');
    link.style.color = '#000';
  }
});
