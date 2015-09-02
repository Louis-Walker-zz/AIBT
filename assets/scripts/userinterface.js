! function bagModule() {

  var $modules = document.querySelectorAll("[data-moduletype=expand]");

  for (var i = 0; $modules.length > i; i++ ) {

    $modules[i].childNodes[1].addEventListener("click", expanderClick);
    $modules[i].childNodes[1].addEventListener("mouseenter", expanderEnter);
    $modules[i].childNodes[1].addEventListener("mouseleave", expanderLeave);

  }

  function expanderEnter() {

    var focusedModule = this.parentNode;

    if ( focusedModule.offsetHeight < 57 ) {

      focusedModule.style.height = "56px";

    }

  }

  function expanderLeave() {

    var focusedModule = this.parentNode;

    if ( focusedModule.offsetHeight < 57 ) {

      focusedModule.style.height = "50px";

    }

  }

  function expanderClick() {

    var currentHeight = this.parentNode.offsetHeight,
        focusedModule = this.parentNode,
        focusedExpander = this;

    if ( currentHeight < 57 ) {

      focusedModule.style.height = "400px";

      focusedExpander.classList.add("module-expander-clicked");
      focusedExpander.innerHTML = "-";

    } else if ( currentHeight == 400 ) {

      focusedModule.style.height = "56px";

      focusedExpander.classList.remove("module-expander-clicked");
      focusedExpander.innerHTML = "More Info";

    }

  }

}();
