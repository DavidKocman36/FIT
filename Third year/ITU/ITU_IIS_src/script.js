/**
 * Author: Tomáš Souček, xsouce15
 */
let nav = document.querySelector('.side-nav');
let links = [...nav.children].splice(2 ,(nav.children.length-1));

for(let i = 0; i < links.length; i++)
{
    if(window.location.href.includes(links[i].id))
    {
        console.log(links[i].id);
        links[i].classList.add('active');
    }
}


function successAnimation()
{
    $('body').append('<div class="success-animation">    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" /><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" /></svg>    </div>');
}
