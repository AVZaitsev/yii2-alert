var vis = (function () {
  var stateKey,
    eventKey,
    keys = {
      hidden: "visibilitychange",
      webkitHidden: "webkitvisibilitychange",
      mozHidden: "mozvisibilitychange",
      msHidden: "msvisibilitychange",
    };
  for (stateKey in keys) {
    if (stateKey in document) {
      eventKey = keys[stateKey];
      break;
    }
  }
  return function (c) {
    if (c) document.addEventListener(eventKey, c);
    return !document[stateKey];
  };
})();

function canStartSse() {
  const allowedProtocols = ["h2", "h3"];
  const entries = performance.getEntries();
  let currentProtocol = "http/1.0";

  for (let i = 0; i < entries.length; i++) {
    if (
      PerformanceNavigationTiming.prototype.isPrototypeOf(
        performance.getEntries()[i]
      )
    ) {
      currentProtocol = entries[i].nextHopProtocol;
      break;
    }
  }
  if (allowedProtocols.includes(currentProtocol)) {
    return true;
  }
  console.warn("Can't start SSE on " + currentProtocol + ". Only for h2 or h3");
  return false;
}

// Listen for checkout events and update the alert section accordingly.
function listenCheckoutEvents() {
  let source = startSse();
  vis(function () {
    if (vis()) {
      if (source.readyState == EventSource.CLOSED) {
        source = startSse();
      }
    } else {
      source.close();
    }
  });
}

function startSse() {
  const source = new EventSource("/alert/default/index");

  source.onmessage = function (e) {
    const data = JSON.parse(e.data);

    if (data.html.length) {
      const parser = new DOMParser();
      const doc = parser.parseFromString(data.html, "text/html");
      const alertContent = doc.querySelector("#avzaitsau-alert").innerHTML;
      document.querySelector("#avzaitsau-alert").innerHTML += alertContent;
    }
  };

  window.addEventListener("beforeunload", function (e) {
    source.close();
  });

  return source;
}

if (canStartSse()) listenCheckoutEvents();
