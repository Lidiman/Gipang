document.addEventListener('DOMContentLoaded', () => {
  const profile = document.querySelector('.nav-profile');
  if (!profile) {
    console.log('nav-profile element not found');
    return;
  }
  const dropdown = profile.querySelector('.nav-profile-dropdown');
  if (!dropdown) {
    console.log('nav-profile-dropdown element not found');
    return;
  }
  console.log('Attaching mouseenter and mouseleave events');
  profile.addEventListener('mouseenter', () => {
    console.log('mouseenter event fired');
    dropdown.classList.add('active');
  });
  profile.addEventListener('mouseleave', () => {
    console.log('mouseleave event fired');
    dropdown.classList.remove('active');
  });
});
