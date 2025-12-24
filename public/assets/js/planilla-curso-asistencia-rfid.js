const containerImg = document.getElementById('container-img');
const estudiante = document.getElementById('estudiante-id');
const inpRFID = document.getElementById('rfid');

const eventos = document.querySelectorAll('input[type=radio]');
eventos.forEach((input) => {
  input.addEventListener('change', () => {
    document.getElementById('rfid').focus();
  })
})


const saludarAEstudiante = (texto) => {
  const voz = new SpeechSynthesisUtterance(texto);
  voz.lang = 'es-BO';
  voz.volume = 1;
  window.speechSynthesis.speak(voz);
}

const cloneSvgFromTemplate = (templateId) => {
  const template = document.querySelector('template');
  return template.content.querySelector(`#${templateId}`).cloneNode(true);
}

const setImageToContainer = (imgNameId) => {
  containerImg.innerHTML = '';
  const img = cloneSvgFromTemplate(imgNameId);
  containerImg.appendChild(img);
}

inpRFID.addEventListener('keypress', async function (event) {
  if (event.key === 'Enter') {
    event.preventDefault();
    const formData = new FormData(this.form);
    const data = Object.fromEntries(formData.entries());
    try {
      const response = await fetch('/asistencia/planilla/curso/rfid', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
      });

      if (!response.ok) {
        throw new Error('Error en la petición');
      }

      response.json()
        .then((data) => {
          setImageToContainer('img-ok');
          const estudianteDTO = data.estudiante;
          const saludo = estudianteDTO.nombres || '';
          const inputOutput = document.querySelector('input[type=radio]:checked');
          const prefijoSaludo = inputOutput.value === 'ingreso' ? 'Hola ' : 'Chau ';
          estudiante.textContent = `${estudianteDTO.apellidos || ''} ${estudianteDTO.nombres || ''}`;
          saludarAEstudiante(prefijoSaludo + saludo);
          setTimeout(() => {
            this.value = '';
            estudiante.textContent = '.';
            this.focus();
            setImageToContainer('img-input');
          }, 1500);
        });
    } catch (error) {
      setImageToContainer('img-error');
      setTimeout(() => {
        this.value = '';
        estudiante.textContent = '.';
        this.focus();
        setImageToContainer('img-input');
      }, 1500);
    }
  }
});
