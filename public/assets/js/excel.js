const domain = 'http://156.244.39.167:9000/';

const btnExcel = document.getElementById('btnExcel');
btnExcel.addEventListener('click', () => {
  const grado = document.getElementById('grado').value;
  const paralelo = document.getElementById('paralelo').value;
  const fileName = `${grado} ${paralelo} - CENTRALIZADOR DE NOTAS.xlsx`;

  const formDataSerach = document.getElementById('search-form');
  const formData = new FormData(formDataSerach);

  Swal.fire({
    title: 'Generando archivo excel',
    text: 'Espere unos momentos mientras de genera el reporte',
    didOpen: () => {
      Swal.showLoading();
    },
    allowOutsideClick: false
  });

  fetch(`${domain}/cursos/excel`, {
    method: 'POST',
    body: formData
  })
    .then(response => {
      if (!response.ok) {
        throw new Error('Error en la descarga del archivo');
      }
      return response.blob();
    })
    .then(blob => {
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = fileName;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      window.URL.revokeObjectURL(url);
      Swal.close();
    })
    .catch(error => {
      Swal.close();
      Swal.fire({
        icon: 'info',
        title: 'Problemas en el servidor',
        text: 'Intentelo en unos minutos por favor',
      });
    });
})
