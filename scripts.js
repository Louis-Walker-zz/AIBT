! function bagModule() {

  var $modules = document.querySelectorAll("[data-moduletype=expand]");

  for (var i = 0; $modules.length > i; i++ ) {

    $modules[i].childNodes[1].addEventListener("click", clickExpandable);
    $modules[i].childNodes[1].addEventListener("mouseenter", moduleEnter);
    $modules[i].childNodes[1].addEventListener("mouseleave", moduleLeave);

  }

  function moduleEnter() {

    var focusedModule = this.parentNode;

    console.log(focusedModule.offsetHeight);

    if ( focusedModule.offsetHeight < 57 ) {

      focusedModule.style.height = "56px";

    }

  }

  function moduleLeave() {

    var focusedModule = this.parentNode;

    if ( focusedModule.offsetHeight < 57 ) {

      focusedModule.style.height = "50px";

    }

  }

  function clickExpandable() {

    var currentHeight = this.parentNode.offsetHeight,
        focusedModule = this.parentNode,
        focusedButton = this;

    if ( currentHeight < 57 ) {

      focusedModule.style.height = "400px";

      focusedButton.classList.add("module-exp-btn-clicked");
      focusedButton.innerHTML = "-";

    } else if ( currentHeight == 400 ) {

      focusedModule.style.height = "56px";

      focusedButton.classList.remove("module-exp-btn-clicked");
      focusedButton.innerHTML = "More Info";

    }

  }

}();
