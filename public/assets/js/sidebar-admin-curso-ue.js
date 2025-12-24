document.addEventListener('DOMContentLoaded', () => {
  const inpTrimestre = document.getElementById('trimestre')
  const inpGrado = document.getElementById('grado');
  const inpUnidadEducativa = document.getElementById('unidad-educativa');

  const inpParaleloA = document.getElementById('paralelo-a');
  const inpParaleloB = document.getElementById('paralelo-b');
  const inpParaleloC = document.getElementById('paralelo-c');
  const inpParaleloD = document.getElementById('paralelo-d');
  const inpParaleloE = document.getElementById('paralelo-e');
  const inpParaleloTodos = document.getElementById('paralelo-todos');

  const setChangeValues = (grado, unidadEducativa, trimestre) => {
    inpParaleloA.setAttribute('href', `/ue/${unidadEducativa}/grado/${grado}/paralelo/A/${trimestre}`);
    inpParaleloB.setAttribute('href', `/ue/${unidadEducativa}/grado/${grado}/paralelo/B/${trimestre}`);
    inpParaleloC.setAttribute('href', `/ue/${unidadEducativa}/grado/${grado}/paralelo/C/${trimestre}`);
    inpParaleloD.setAttribute('href', `/ue/${unidadEducativa}/grado/${grado}/paralelo/D/${trimestre}`);
    inpParaleloE.setAttribute('href', `/ue/${unidadEducativa}/grado/${grado}/paralelo/E/${trimestre}`);
    inpParaleloTodos.setAttribute('href', `/ue/${unidadEducativa}/grado/${grado}/paralelo/TODOS/${trimestre}`);
    localStorage.setItem('jvrf-unidad-educativa-id', unidadEducativa);
    localStorage.setItem('jvrf-grado-id', grado);
    localStorage.setItem('jvrf-trimestre', trimestre);
  }

  inpTrimestre.addEventListener('change', (evt) => {
    const trimestre = evt.target.value;
    const grado = inpGrado.value;
    const unidadEducativa = inpUnidadEducativa.value;
    setChangeValues(grado, unidadEducativa, trimestre);
  });

  inpGrado.addEventListener('change', (evt) => {
    const grado = evt.target.value;
    const unidadEducativa = inpUnidadEducativa.value;
    const trimestre = inpTrimestre.value;
    setChangeValues(grado, unidadEducativa, trimestre);
  });

  inpUnidadEducativa.addEventListener('change', (evt) => {
    const grado = inpGrado.value;
    const unidadEducativa = evt.target.value;
    const trimestre = inpTrimestre.value;
    setChangeValues(grado, unidadEducativa, trimestre);
  });

  const unidadEducativa = localStorage.getItem('jvrf-unidad-educativa-id') || 1;
  const grado = localStorage.getItem('jvrf-grado-id') || 4;
  const trimestre = localStorage.getItem('jvrf-trimestre') || 1;

  inpUnidadEducativa.value = unidadEducativa;
  inpGrado.value = grado;
  inpTrimestre.value = trimestre;
  setChangeValues(grado, unidadEducativa, trimestre);
});
