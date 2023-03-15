
// Navigation Animations...

const navSlide = () => {
    const burger = document.querySelector('.burger');
    const nav = document.querySelector('.nav-links');
    const navLinks = document.querySelectorAll('.nav-links li');

    // Toggle Navigation...
    if (burger) {
        burger.addEventListener('click', () =>{
            nav.classList.toggle('nav-active');

            //Animate Links...
            navLinks.forEach((link, index) => {
                if(link.style.animation){
                    link.style.animation = '';
                }
                else{
                    link.style.animation = `navLinkFade 0.5s ease forwards ${index / 7 + 0.1}s`;
                }
            });

            //Burger Animation...
            burger.classList.toggle('toggle');
        });
    }
}


// Form Modal...

const showForm = () => {
    // Variable Declaration...
    const formTrigger = document.querySelector('#form-trigger');
    const formModal = document.querySelector('.form-modal');

    // Event Listener to the Trigger button...
    if (formTrigger) {
        formTrigger.addEventListener('click', () => {
            formModal.classList.toggle('show-form');
        });
    }
}


// Function Calling...

navSlide();
showForm();



// Error Messages...

function dangerErrorMessage (messageToShow){
    // Declaration of necessary variables...
    this.messageToShow = messageToShow;
    const errorHolder = document.querySelector('#error-holder');


    // Creating Elements...
    const errorDiv = document.createElement('div');
    const errorMessage = document.createElement('p');
    const errorIcon = document.createElement('i');
    const closeIcon = document.createElement('i');

    // Adding The Error Messages...
    errorMessage.textContent = this.messageToShow;

    // Adding Classes to The icon & errorDiv...
    errorIcon.classList.add('icon_error-circle_alt');
    errorDiv.classList.add('error');
    closeIcon.classList.add('icon_close');
    closeIcon.id = 'close-btn';
    closeIcon.setAttribute('title', 'Dismiss');

    // Appending Children...

    errorDiv.append(errorIcon,errorMessage,closeIcon);
    errorHolder.appendChild(errorDiv);

    const closeBtn = document.querySelector('#close-btn');

    setTimeout(() => {
        errorDiv.remove();
    }, 10000);

    closeBtn.addEventListener('click', () => {
        errorDiv.remove();
    });

}


function successMessage (messageToShow){
    // Declaration of necessary variables...
    this.messageToShow = messageToShow;
    const successHolder = document.querySelector('#error-holder');


    // Creating Elements...
    const successDiv = document.createElement('div');
    const successMessage = document.createElement('p');
    const successIcon = document.createElement('i');
    const closeIcon = document.createElement('i');

    // Adding The Error Messages...
    successMessage.textContent = this.messageToShow;

    // Adding Classes to The icon & errorDiv...
    successIcon.classList.add('icon_check_alt2');
    successDiv.classList.add('success');
    closeIcon.classList.add('icon_close');
    closeIcon.id = 'close-btn';
    closeIcon.setAttribute('title', 'Dismiss');

    // Appending Children...

    successDiv.append(successIcon,successMessage,closeIcon);
    successHolder.appendChild(successDiv);

    const closeBtn = document.querySelector('#close-btn');

    setTimeout(() => {
        successDiv.remove();
    }, 10000);

    closeBtn.addEventListener('click', () => {
        successDiv.remove();
    });

}

// Closing Btns
const closeModalBtn = document.querySelector('#close-modal');
const closeModalBtn2 = document.querySelector('#close-modal2');
const closeModalBtn3 = document.querySelector('#close-modal3');

// opening Buttons
const openModalBtn = document.querySelector('#modal-trigger');
const openModalBtn2 = document.querySelector('#modal-trigger2');
const openModalBtn3 = document.querySelector('#modal-trigger3');


// Closing The First modal...
if (closeModalBtn) {
    // Declaring Necessary Variables...
    const modalContent = document.querySelector('.modal-content');
    const modal = document.querySelector('.modal');

    closeModalBtn.addEventListener('click', () => {
        modal.classList.add('closing-modal');
        setTimeout(() => {
            modalContent.style.display = 'none';
        }, 200);
    });
}

// Closing The Second modal...
if (closeModalBtn3) {
    // Declaring Necessary Variables...
    const modalContent2 = document.querySelector('.modal-content2');
    const modal2 = document.querySelector('.modal2');

    closeModalBtn2.addEventListener('click', () => {
        modal2.classList.add('closing-modal');
        setTimeout(() => {
            modalContent2.style.display = 'none';
        }, 200);
    });
}

// Closing The Third modal...
if (closeModalBtn3) {
    // Declaring Necessary Variables...
    const modalConten3 = document.querySelector('.modal-content3');
    const modal3 = document.querySelector('.modal');

    closeModalBtn3.addEventListener('click', () => {
        modal3.classList.add('closing-modal');
        setTimeout(() => {
            modalConten3.style.display = 'none';
        }, 200);
    });
}


    //  Opening The First Modal...

if (openModalBtn) {
    const modalContent = document.querySelector('.modal-content');
    const modal = document.querySelector('.modal');

    openModalBtn.addEventListener('click', () => {
        modal.classList.add('opening-modal');
        setTimeout(() => {
            modalContent.style.display = 'flex';
        }, 1);
    });
}

    // Opening The Second Modal...

if (openModalBtn2) {
    const modalContent2 = document.querySelector('.modal-content2');
    const modal2 = document.querySelector('.modal2');

    openModalBtn2.addEventListener('click', () => {
        modal2.classList.add('opening-modal');
        setTimeout(() => {
            modalContent2.style.display = 'flex';
        }, 1);
    });
}

    // Opening The Third Modal...

if (openModalBtn3) {
    const modalContent3 = document.querySelector('.modal-content3');
    const modal3 = document.querySelector('.modal3');

    openModalBtn3.addEventListener('click', () => {
        modal3.classList.add('opening-modal');
        setTimeout(() => {
            modalContent3.style.display = 'flex';
        }, 1);
    });
}
