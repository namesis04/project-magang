function logout(p = '.') {
    const xhr = new XMLHttpRequest;
    xhr.open('post', p + '/logout.php');
    xhr.send();
    xhr.addEventListener('load', function r() {
        xhr.removeEventListener('load', r);
        location.reload();
    });
}
