/* Nexabie CMS — admin behaviour */

(function () {
  "use strict";

  // Repeatable lists: add / remove rows
  document.addEventListener("click", function (e) {
    var addBtn = e.target.closest("[data-add]");
    if (addBtn) {
      var block = addBtn.closest("[data-list]");
      var tpl = block && block.querySelector("template");
      if (!tpl) return;
      var stamp = Date.now().toString(36) + Math.floor(Math.random() * 1000).toString(36);
      var html = tpl.innerHTML.replace(/__I(\d+)__/g, function (m, d) {
        return d + "_" + stamp;
      });
      var tmp = document.createElement("template");
      tmp.innerHTML = html;
      block.querySelector(".list-rows").appendChild(tmp.content.cloneNode(true));
      return;
    }

    var delBtn = e.target.closest("[data-remove]");
    if (delBtn) {
      var row = delBtn.closest(".list-row");
      if (row) row.remove();
    }
  });

  // Click a media path to copy it
  document.addEventListener("click", function (e) {
    var pathBtn = e.target.closest("[data-copy]");
    if (!pathBtn) return;
    var text = pathBtn.getAttribute("data-copy") || "";
    var done = function () {
      var old = pathBtn.textContent;
      pathBtn.textContent = "Copied!";
      setTimeout(function () {
        pathBtn.textContent = old;
      }, 1200);
    };
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(done, done);
    } else {
      var ta = document.createElement("textarea");
      ta.value = text;
      document.body.appendChild(ta);
      ta.select();
      try {
        document.execCommand("copy");
      } catch (err) {}
      ta.remove();
      done();
    }
  });
})();
