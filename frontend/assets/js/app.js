const API_BASE = 'http://localhost:8080/api';
const apiLabel = document.getElementById('apiLabel');
if (apiLabel) apiLabel.textContent = API_BASE;

const entities = {
  entrenadores: {
    label: 'Entrenadores', icon: '💪',
    fields: ['nombre', 'especialidad', 'email', 'telefono', 'activo'],
    help: 'Entidad primaria. Las clases usan entrenador_id, por eso primero deben existir entrenadores.'
  },
  planes: {
    label: 'Planes', icon: '🎫',
    fields: ['nombre', 'descripcion', 'precio_mensual', 'duracion_dias', 'activo'],
    help: 'Entidad primaria. Los miembros usan plan_id, por eso primero deben existir planes.'
  },
  miembros: {
    label: 'Miembros', icon: '🧍',
    fields: ['plan_id', 'nombre', 'email', 'telefono', 'fecha_inscripcion', 'estado'],
    help: 'Entidad secundaria. plan_id debe existir en Planes. Estados permitidos: ACTIVO, INACTIVO, SUSPENDIDO.'
  },
  clases: {
    label: 'Clases', icon: '🏃',
    fields: ['entrenador_id', 'nombre', 'descripcion', 'cupo_maximo', 'horario', 'activo'],
    help: 'Entidad secundaria. entrenador_id debe existir en Entrenadores.'
  },
  reservas: {
    label: 'Reservas', icon: '📅',
    fields: ['miembro_id', 'clase_id', 'fecha_reserva', 'estado'],
    help: 'Entidad secundaria. miembro_id y clase_id deben existir. Estados permitidos: RESERVADA, ASISTIO, CANCELADA.'
  }
};

const labels = {
  id: 'ID', nombre: 'Nombre', especialidad: 'Especialidad', email: 'Correo', telefono: 'Teléfono', activo: 'Activo',
  descripcion: 'Descripción', precio_mensual: 'Precio mensual', duracion_dias: 'Duración días', plan_id: 'ID plan',
  fecha_inscripcion: 'Fecha inscripción', estado: 'Estado', entrenador_id: 'ID entrenador', cupo_maximo: 'Cupo máximo',
  horario: 'Horario', miembro_id: 'ID miembro', clase_id: 'ID clase', fecha_reserva: 'Fecha reserva',
  created_at: 'Creado', updated_at: 'Actualizado'
};

let current = 'entrenadores';
let editingId = null;

const tabs = document.getElementById('tabs');
const form = document.getElementById('entityForm');
const thead = document.getElementById('thead');
const tbody = document.getElementById('tbody');
const message = document.getElementById('message');
const search = document.getElementById('search');
const tableTitle = document.getElementById('tableTitle');
const relationHelp = document.getElementById('relationHelp');

function human(field) { return labels[field] || field; }
function escapeHtml(value) {
  return String(value ?? '').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));
}
function showMessage(text, type = 'ok') {
  message.textContent = text;
  message.className = `message ${type}`;
  if (text) setTimeout(() => { message.textContent = ''; message.className = 'message'; }, 5200);
}
function inputType(field) {
  if (['precio_mensual'].includes(field)) return 'number';
  if (['duracion_dias', 'cupo_maximo', 'plan_id', 'entrenador_id', 'miembro_id', 'clase_id'].includes(field)) return 'number';
  if (['horario', 'fecha_reserva'].includes(field)) return 'datetime-local';
  if (field === 'fecha_inscripcion') return 'date';
  if (field === 'email') return 'email';
  return 'text';
}
function normalizeDateValue(field, value) {
  if (!value) return '';
  if (field === 'fecha_inscripcion') return String(value).slice(0, 10);
  if (['horario', 'fecha_reserva'].includes(field)) return String(value).replace(' ', 'T').slice(0, 16);
  return value;
}
function renderTabs() {
  tabs.innerHTML = Object.entries(entities).map(([key, entity]) => `
    <button class="tab ${key === current ? 'active' : ''}" type="button" onclick="selectEntity('${key}')">
      <span>${entity.icon} ${entity.label}</span><span>›</span>
    </button>`).join('');
}
function renderRelationHelp() {
  relationHelp.innerHTML = `<strong>Tip:</strong> ${entities[current].help}`;
}
function fieldControl(field, value) {
  const val = normalizeDateValue(field, value ?? '');
  if (field === 'activo') {
    return `<select name="activo"><option value="true" ${value === true || value === 'true' ? 'selected' : ''}>Sí</option><option value="false" ${value === false || value === 'false' ? 'selected' : ''}>No</option></select>`;
  }
  if (field === 'estado' && current === 'miembros') {
    return `<select name="estado">${['ACTIVO','INACTIVO','SUSPENDIDO'].map(s => `<option value="${s}" ${val === s ? 'selected' : ''}>${s}</option>`).join('')}</select>`;
  }
  if (field === 'estado' && current === 'reservas') {
    return `<select name="estado">${['RESERVADA','ASISTIO','CANCELADA'].map(s => `<option value="${s}" ${val === s ? 'selected' : ''}>${s}</option>`).join('')}</select>`;
  }
  const step = field === 'precio_mensual' ? ' step="0.01" min="0"' : '';
  const min = ['duracion_dias','cupo_maximo','plan_id','entrenador_id','miembro_id','clase_id'].includes(field) ? ' min="1"' : '';
  const required = ['nombre','especialidad','email','precio_mensual','duracion_dias','plan_id','fecha_inscripcion','estado','entrenador_id','cupo_maximo','horario','miembro_id','clase_id','fecha_reserva'].includes(field) ? ' required' : '';
  return `<input name="${field}" type="${inputType(field)}" value="${escapeHtml(val)}"${step}${min}${required} />`;
}
function renderForm(data = {}) {
  const title = `${editingId ? 'Editar' : 'Crear'} ${entities[current].label}`;
  form.innerHTML = `
    <div class="form-title"><p class="eyebrow">Formulario</p><h2>${title}</h2><p class="muted">Completa los campos y guarda el registro.</p></div>
    <div class="form-grid">
      ${entities[current].fields.map(field => `<div class="field"><label>${human(field)}</label>${fieldControl(field, data[field])}</div>`).join('')}
    </div>
    <div class="form-actions">
      <button class="primary" type="submit">${editingId ? 'Actualizar registro' : 'Crear registro'}</button>
      <button class="secondary" type="button" onclick="resetForm()">Limpiar</button>
    </div>`;
}
async function loadData() {
  try {
    const term = search.value.trim();
    const url = term ? `${API_BASE}/${current}/filter?search=${encodeURIComponent(term)}` : `${API_BASE}/${current}`;
    const response = await axios.get(url);
    renderTable(Array.isArray(response.data) ? response.data : []);
  } catch (err) {
    showMessage('Error cargando datos: ' + (err.response?.data?.detail || err.response?.data?.error || err.message), 'error');
  }
}
function formatCell(key, value) {
  if (value === true || value === 'true') return '<span class="badge">Sí</span>';
  if (value === false || value === 'false') return '<span class="badge off">No</span>';
  if (['created_at','updated_at','horario','fecha_reserva'].includes(key) && value) return escapeHtml(String(value).replace('T', ' ').slice(0, 19));
  return escapeHtml(value ?? '');
}
function renderTable(rows) {
  tableTitle.textContent = entities[current].label;
  const cols = rows[0] ? Object.keys(rows[0]) : ['id', ...entities[current].fields];
  thead.innerHTML = `<tr>${cols.map(c => `<th>${human(c)}</th>`).join('')}<th>Acciones</th></tr>`;
  if (!rows.length) {
    tbody.innerHTML = `<tr><td class="empty" colspan="${cols.length + 1}">No hay registros para mostrar.</td></tr>`;
    return;
  }
  tbody.innerHTML = rows.map(row => {
    const encoded = encodeURIComponent(JSON.stringify(row));
    return `<tr>${cols.map(c => `<td>${formatCell(c, row[c])}</td>`).join('')}
      <td><div class="row-actions"><button class="secondary" type="button" onclick="editRow('${encoded}')">Editar</button><button class="danger" type="button" onclick="deleteRow(${row.id})">Eliminar</button></div></td></tr>`;
  }).join('');
}
function selectEntity(key) {
  current = key;
  editingId = null;
  search.value = '';
  renderTabs();
  renderRelationHelp();
  renderForm();
  loadData();
}
function resetForm() { editingId = null; renderForm(); }
function editRow(encodedRow) {
  const row = JSON.parse(decodeURIComponent(encodedRow));
  editingId = row.id;
  renderForm(row);
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
async function deleteRow(id) {
  if (!confirm('¿Eliminar este registro?')) return;
  try {
    await axios.delete(`${API_BASE}/${current}/${id}`);
    showMessage('Registro eliminado correctamente.');
    loadData();
  } catch (err) {
    showMessage('No se pudo eliminar: ' + (err.response?.data?.detail || err.response?.data?.error || err.message), 'error');
  }
}
function buildPayload() {
  const payload = {};
  new FormData(form).forEach((value, key) => {
    if (value === '') return;
    if (value === 'true' || value === 'false') payload[key] = value === 'true';
    else if (['precio_mensual'].includes(key)) payload[key] = Number(value);
    else if (['duracion_dias','cupo_maximo','plan_id','entrenador_id','miembro_id','clase_id'].includes(key)) payload[key] = Number(value);
    else payload[key] = value;
  });
  return payload;
}
form.addEventListener('submit', async event => {
  event.preventDefault();
  const payload = buildPayload();
  try {
    if (editingId) await axios.put(`${API_BASE}/${current}/${editingId}`, payload);
    else await axios.post(`${API_BASE}/${current}`, payload);
    showMessage(editingId ? 'Registro actualizado correctamente.' : 'Registro creado correctamente.');
    resetForm();
    loadData();
  } catch (err) {
    showMessage('Error guardando: ' + (err.response?.data?.detail || err.response?.data?.error || err.message), 'error');
  }
});
search.addEventListener('input', () => loadData());
renderTabs();
renderRelationHelp();
renderForm();
loadData();
