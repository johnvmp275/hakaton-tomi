document.addEventListener("DOMContentLoaded", () => {
  const selectLocal = document.getElementById("local_atendimento");

  if (selectLocal) {
    selectLocal.addEventListener("change", () => {
      const selectedValue = selectLocal.value;
      updateQueryString("local_atendimento", selectedValue);
    });
  }
});

function updateQueryString(key, value) {
  const url = new URL(window.location);

  if (value) {
    url.searchParams.set(key, value);
  } else {
    url.searchParams.delete(key);
  }

  window.location.href = url;
}

function enviarData() {
  const dataSelecionada = document.getElementById("dataAno").value;

  if (dataSelecionada) {
    const data = new Date(dataSelecionada);
    const anoSelecionado = data.getFullYear();

    updateQueryString("dataAno", anoSelecionado);
  }
}

function enviarDataAnoAtendimento() {
  const dataSelecionada = document.getElementById("dataAnoAtendimento").value;

  if (dataSelecionada) {
    const data = new Date(dataSelecionada);
    const anoSelecionado = data.getFullYear();

    updateQueryString("dataAnoAtendimento", anoSelecionado);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const numeros = document.querySelectorAll("p[data-numero]");

  if (numeros) {
    numeros.forEach((numero) => {
      const total = +numero.innerText;
      if (total > 0) {
        const incremento = Math.floor(total / 100) || 1;
        let start = 0;
        const timer = setInterval(() => {
          start = start + incremento;
          numero.innerText = start;
          if (start > total) {
            numero.innerText = total;
            clearInterval(timer);
          }
        }, 25 * Math.random());
      }
    });
  }
});

const tabcontent = document.getElementsByClassName("tabcontent");

if (tabcontent) {
  const tablinks = document.getElementsByClassName("tablinks");

  function openCity(evt, cityName) {
    let i;

    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }

    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";

    localStorage.setItem("activeTab", cityName);
  }

  document.addEventListener("DOMContentLoaded", () => {
    const savedTab = localStorage.getItem("activeTab");
    let isValidTab = false;

    const allTabContentDivs = document.querySelectorAll('.tabcontent');

    allTabContentDivs.forEach((tab) => {
      if (tab.id === savedTab) {
        isValidTab = true; 
      }
    });

    if (isValidTab) {
      const tabToShow = document.getElementById(savedTab);
      if (tabToShow) {
        tabToShow.style.display = "block";
        for (let i = 0; i < tablinks.length; i++) {
          if (tablinks[i].getAttribute("onclick").includes(savedTab)) {
            tablinks[i].className += " active";
          }
        }
      }
    } else {
      tabcontent[0].style.display = "block";
      tablinks[0].className += " active";
    }
  });
}
