
document.addEventListener('DOMContentLoaded', function() {
    console.log('JavaScript chargé avec succès !');
    
   
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', validateRegistration);
    }
    
 
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', validateLogin);
    }
    
  
    const groupForm = document.getElementById('groupForm');
    if (groupForm) {
        groupForm.addEventListener('submit', validateGroupForm);
    }
    

    const searchInput = document.getElementById('searchGroups');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            searchGroups(this.value);
        });
    }
    
    
    attachJoinGroupListeners();
});


function attachJoinGroupListeners() {
    const joinButtons = document.querySelectorAll('.join-group-btn');
    console.log('Boutons "Rejoindre" trouvés :', joinButtons.length);
    
    joinButtons.forEach(button => {
        button.removeEventListener('click', handleJoinGroup);
        button.addEventListener('click', handleJoinGroup);
        console.log('Listener ajouté pour groupe ID:', button.dataset.groupId);
    });
}


function handleJoinGroup(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const groupId = this.dataset.groupId;
    console.log('Clic sur "Rejoindre" pour le groupe ID:', groupId);
    
    if (!groupId) {
        showMessage('error', 'ID du groupe non trouvé.');
        return;
    }
    
    if (confirm('Voulez-vous vraiment rejoindre ce groupe ?')) {
        joinGroup(groupId);
    }
}


function validateRegistration(e) {
    e.preventDefault();
    
    const username = document.getElementById('username');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    clearErrors();
    
    let isValid = true;
    let errorMessages = [];
    
    if (!username.value.trim()) {
        errorMessages.push("Nom d'utilisateur requis");
        isValid = false;
    } else if (username.value.length < 3) {
        errorMessages.push("Le nom d'utilisateur doit contenir au moins 3 caractères");
        isValid = false;
    }
    
    if (!email.value.trim()) {
        errorMessages.push("Email requis");
        isValid = false;
    } else if (!isValidEmail(email.value)) {
        errorMessages.push("Veuillez entrer une adresse email valide");
        isValid = false;
    }
    
    if (!password.value) {
        errorMessages.push("Mot de passe requis");
        isValid = false;
    } else if (password.value.length < 6) {
        errorMessages.push("Le mot de passe doit contenir au moins 6 caractères");
        isValid = false;
    }
    
    if (password.value !== confirmPassword.value) {
        errorMessages.push("Les mots de passe ne correspondent pas");
        isValid = false;
    }
    
    if (!isValid) {
        showErrors(errorMessages);
    } else {
        checkEmailExists(email.value, function(exists) {
            if (exists) {
                showErrors(["Cet email est déjà utilisé. Veuillez en utiliser un autre."]);
            } else {
                e.target.submit();
            }
        });
    }
}


function validateLogin(e) {
    e.preventDefault();
    
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    
    clearErrors();
    let errorMessages = [];
    
    if (!email.value.trim()) {
        errorMessages.push("Email requis");
    }
    
    if (!password.value) {
        errorMessages.push("Mot de passe requis");
    }
    
    if (errorMessages.length > 0) {
        showErrors(errorMessages);
    } else {
        e.target.submit();
    }
}


function validateGroupForm(e) {
    e.preventDefault();
    
    const name = document.getElementById('name');
    const subject = document.getElementById('subject');
    const level = document.getElementById('level');
    
    clearErrors();
    let errorMessages = [];
    
    if (!name.value.trim()) {
        errorMessages.push("Nom du groupe requis");
    } else if (name.value.length < 3) {
        errorMessages.push("Le nom du groupe doit contenir au moins 3 caractères");
    }
    
    if (!subject.value) {
        errorMessages.push("Veuillez sélectionner une matière");
    }
    
    if (!level.value) {
        errorMessages.push("Veuillez sélectionner un niveau");
    }
    
    if (errorMessages.length > 0) {
        showErrors(errorMessages);
    } else {
        e.target.submit();
    }
}


function checkEmailExists(email, callback) {
    fetch('ajax/check_email.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'email=' + encodeURIComponent(email)
    })
    .then(response => response.json())
    .then(data => {
        callback(data.exists);
    })
    .catch(error => {
        console.error('Erreur:', error);
        callback(false);
    });
}


function searchGroups(keyword) {
    const groupsContainer = document.getElementById('groupsList');
    if (!groupsContainer) return;
    
    groupsContainer.innerHTML = '<div class="text-center"><p>Recherche en cours...</p></div>';
    
    fetch('ajax/get_groups.php?search=' + encodeURIComponent(keyword))
        .then(response => response.text())
        .then(html => {
            groupsContainer.innerHTML = html;
            attachJoinGroupListeners();
        })
        .catch(error => {
            console.error('Erreur:', error);
            groupsContainer.innerHTML = '<p>Erreur lors de la recherche.</p>';
        });
}


function joinGroup(groupId) {
    console.log('Envoi requête pour groupe:', groupId);
    
    const button = document.querySelector(`.join-group-btn[data-group-id="${groupId}"]`);
    const originalText = button ? button.innerHTML : 'Rejoindre';
    if (button) {
        button.innerHTML = 'Chargement...';
        button.disabled = true;
    }
    
    fetch('ajax/join_group.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'group_id=' + encodeURIComponent(groupId)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Réponse:', data);
        if (data.success) {
            showMessage('success', data.message);
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showMessage('error', data.message);
            if (button) {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showMessage('error', 'Une erreur est survenue.');
        if (button) {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
}


function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function clearErrors() {
    const existingErrors = document.querySelectorAll('.alert-error');
    existingErrors.forEach(error => error.remove());
}

function showErrors(messages) {
    const form = document.querySelector('form');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-error';
    errorDiv.innerHTML = messages.join('<br>');
    form.insertBefore(errorDiv, form.firstChild);
}

function showMessage(type, message) {
    const existingMessages = document.querySelectorAll('.alert');
    existingMessages.forEach(msg => msg.remove());
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `alert alert-${type}`;
    messageDiv.style.position = 'fixed';
    messageDiv.style.top = '20px';
    messageDiv.style.right = '20px';
    messageDiv.style.zIndex = '9999';
    messageDiv.style.maxWidth = '350px';
    messageDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
    messageDiv.innerHTML = message;
    
    document.body.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}