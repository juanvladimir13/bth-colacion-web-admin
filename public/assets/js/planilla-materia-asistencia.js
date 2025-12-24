const getFormData = (formId) => {
  const $planillaHoraIngreso = document.getElementById('planilla-hora-ingreso');
  const $planillaHoraSalida = document.getElementById('planilla-hora-salida');

  const $form = document.getElementById(`asistencia-form-item-${formId}`);
  const id = $form.getAttribute('data-asistencia-id');
  const eventualidadId = $form.getAttribute('data-asistencia-eventualidad-id');
  const $estado = $form.querySelector('input[name="estado"]:checked');
  const $ingreso = $form.querySelector('input[name="hora_ingreso"]');
  const $salida = $form.querySelector('input[name="hora_salida"]');
  const $eventualidad = $form.querySelector('textarea[name="eventualidad"]');

  return {
    id,
    formId,
    estado: $estado.value,
    hora_ingreso: $ingreso.value,
    hora_salida: $salida.value,
    eventualidad: $eventualidad.value,
    planilla_hora_ingreso: $planillaHoraIngreso.textContent,
    planilla_hora_salida: $planillaHoraSalida.textContent,
    eventualidad_id: eventualidadId
  }
}

const sendData = (data) => {
  fetch('/asistencia/api/materia', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  })
    .then(response => {
      if (response.status === 201) {
        response.json().then((row) => {
          const $form = document.getElementById(`asistencia-form-item-${data.formId}`);
          $form.setAttribute('data-asistencia-eventualidad-id', row.eventualidad_id || 0);
        });

        const $estudiante = document.getElementById(`estado-color-${data.formId}`);
        $estudiante.classList.remove('text-danger', 'text-success', 'text-info', 'text-warning');

        if (data.estado === 'P' || data.estado === 'A') {
          $estudiante.classList.add('text-success');
        }

        if (data.estado === 'L') {
          $estudiante.classList.add('text-info');
        }

        if (data.estado === 'F') {
          $estudiante.classList.add('text-warning');
        }

        if (data.estado === 'F' || data.estado === 'L') {
          const $entrada = document.getElementById(`hora-ingreso-${data.formId}`);
          const $salida = document.getElementById(`hora-salida-${data.formId}`);
          $entrada.value = '';
          $salida.value = '';
        }
      }

      if (response.status === 400) {
        const $estudiante = document.getElementById(`estado-color-${data.formId}`);
        $estudiante.classList.remove('text-danger', 'text-success', 'text-info', 'text-warning');
        $estudiante.classList.add('text-danger');
      }
    })
    .catch((error) => {
      console.error('Error:', error);
    });
}

const $eventualidades = document.querySelectorAll('textarea');
$eventualidades.forEach((item) => {
  item.addEventListener('blur', (e) => {
    const itemElement = e.target;
    if (itemElement.value.trim() === '') {
      return;
    }

    const data = getFormData(itemElement.getAttribute('data-form-id'));
    sendData(data);
  });
});

const $asistencia = document.querySelectorAll('input[type="radio"]');
$asistencia.forEach((item) => {
  item.addEventListener('change', (e) => {
    const itemElement = e.target;
    const data = getFormData(itemElement.getAttribute('data-form-id'));
    sendData(data);
  });
});

const $horarios = document.querySelectorAll('input[type="time"]');
$horarios.forEach((item) => {
  item.addEventListener('change', (e) => {
    const itemElement = e.target;
    const data = getFormData(itemElement.getAttribute('data-form-id'));
    sendData(data);
  });
});

function capturarYDescargar() {
  html2canvas(document.getElementById('asistencia-reporte'))
    .then(function (canvas) {
      const imgData = canvas.toDataURL('image/png');
      const link = document.createElement('a');
      const date = new Date();

      link.download = `lista-asistencia-${date.getDate() + 1}-${date.getMonth() + 1}-${date.getFullYear()}.png`;
      link.href = imgData;
      link.click();
    });
}
