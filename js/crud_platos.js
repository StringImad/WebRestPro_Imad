document.addEventListener("DOMContentLoaded", () => {
  const restSelect = document.getElementById("restSelect");
  const tbody = document.querySelector("#tablaPlatos tbody");
  const addBtn = document.getElementById("addBtn");

  const modal = document.getElementById("modalBackdrop");
  const modalTitle = document.getElementById("modalTitle");
  const f_name = document.getElementById("f_name");
  const f_desc = document.getElementById("f_desc");
  const f_allergen = document.getElementById("f_allergen");
  const f_half = document.getElementById("f_half");
  const f_price = document.getElementById("f_price");
  const f_type = document.getElementById("f_type");
  const cancelBtn = document.getElementById("cancelBtn");
  const saveBtn = document.getElementById("saveBtn");

  let editingId = null;

const openModal = (title) => {
  console.log("Abriendo modal:", title);
  modalTitle.textContent = title;

  // Backdrop visible
  modal.style.setProperty("display", "flex", "important");
  document.body.style.overflow = "hidden";

  // Fuerza visible la caja del modal por si algún CSS la tenía oculta
  const modalBox = document.querySelector("#modalBackdrop .modal");
  if (modalBox) {
    modalBox.style.setProperty("display", "block", "important");
  }

  // DEBUG: comprueba tamaño
  const r = modalBox.getBoundingClientRect();
  console.log("Modal box rect:", r);
};

const closeModal = () => {
  // Oculta backdrop
  modal.style.display = "none";
  modal.style.removeProperty("display");
  document.body.style.overflow = "";

  // Oculta/limpia modal interno
  const modalBox = document.querySelector("#modalBackdrop .modal");
  if (modalBox) modalBox.style.removeProperty("display");

  // Resetea estado y campos
  editingId = null;
  f_name.value = f_desc.value = f_allergen.value = f_half.value = f_price.value = f_type.value = "";
};




  cancelBtn.addEventListener("click", (e) => {
    e.preventDefault();
    closeModal();
  });

  async function loadRestaurants() {
    const res = await fetch("apiRest.php?resource=restaurantes");
    const json = await res.json();
    restSelect.innerHTML = "";
    (json.data || []).forEach(r => {
      const opt = document.createElement("option");
      opt.value = r.id;
      opt.textContent = r.name;
      restSelect.appendChild(opt);
    });
    if (restSelect.value) loadPlates(restSelect.value);
  }

  restSelect.addEventListener("change", e => loadPlates(e.target.value));

  async function loadPlates(id_restaurant) {
    const res = await fetch(`apiRest.php?resource=platos&id_restaurant=${encodeURIComponent(id_restaurant)}`);
    const json = await res.json();
    tbody.innerHTML = "";
    (json.data || []).forEach(p => {
      const tr = document.createElement("tr");
    tr.innerHTML = `
  <td>${escapeHTML(p.plate_name || "")}</td>
  <td>${escapeHTML(p.descrip || "")}</td>
  <td>${escapeHTML(p.allergen || "")}</td>
  <td>${escapeHTML(p.half_price || "")}</td>
  <td>${escapeHTML(p.price || "")}</td>
  <td>${escapeHTML(p.food_type || "")}</td>
  <td class="actions">
    <button type="button" class="btn" data-edit="${p.id}">Editar</button>
    <button type="button" class="btn danger" data-del="${p.id}">Borrar</button>
  </td>`;
      tbody.appendChild(tr);
    });
  }

  addBtn.addEventListener("click", (e) => {
    e.preventDefault();
    editingId = null;
    openModal("Añadir plato");
  });

  saveBtn.addEventListener("click", async (e) => {
    e.preventDefault();
    console.log("Botón Guardar pulsado");
    const payload = {
      plate_name: f_name.value.trim(),
      descrip:    f_desc.value.trim(),
      allergen:   f_allergen.value.trim(),
      half_price: f_half.value.trim(),
      price:      f_price.value.trim(),
      food_type:  f_type.value.trim(),
      id_restaurant: restSelect.value
    };

    console.log("Datos a enviar:", payload);

    if (!payload.plate_name) { 
      alert("El nombre es obligatorio."); 
      return; 
    }

    try {
      let res, resText, json;

      if (editingId) {
        res = await fetch(`apiRest.php?resource=platos&id=${editingId}`, {
          method: "PUT",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });
        resText = await res.text();
        console.log("Respuesta PUT cruda:", resText);
        json = JSON.parse(resText);
      } else {
        res = await fetch(`apiRest.php?resource=platos`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });
        resText = await res.text();
        console.log("Respuesta POST cruda:", resText);
        json = JSON.parse(resText);
      }

      if (json.error) {
        alert(json.error);
        return;
      }

      closeModal();
      loadPlates(restSelect.value);

    } catch (err) {
      console.error("Error procesando la respuesta:", err);
      alert("Error al guardar el plato. Revisa la consola para más detalles.");
    }
  });

  tbody.addEventListener("click", async (e) => {
    const editId = e.target.getAttribute("data-edit");
    const delId  = e.target.getAttribute("data-del");

    if (editId) {
      e.preventDefault();
      console.log("Editando plato:", editId);
      const tr = e.target.closest("tr");
      const tds = tr.querySelectorAll("td");
      editingId = editId;
      f_name.value = tds[0].textContent;
      f_desc.value = tds[1].textContent;
      f_allergen.value = tds[2].textContent;
      f_half.value = tds[3].textContent;
      f_price.value = tds[4].textContent;
      f_type.value = tds[5].textContent;
      openModal("Editar plato");
    }

    if (delId) {
      e.preventDefault();
      if (!confirm("¿Seguro que quieres eliminar este plato?")) return;
      const res = await fetch(`apiRest.php?resource=platos&id=${delId}`, { method:"DELETE" });
      const resText = await res.text();
      console.log("Respuesta DELETE cruda:", resText);
      const json = JSON.parse(resText);
      if (json.error) {
        alert(json.error);
        return;
      }
      loadPlates(restSelect.value);
    }
  });

  function escapeHTML(str){
    return (str ?? "").toString().replace(/[&<>"']/g, s => ({
      "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
    }[s]));
  }

  loadRestaurants();
});
