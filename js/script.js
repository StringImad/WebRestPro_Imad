$(document).ready(function () {
  $(".btn-enter").click(function () {
    alert("¡Botón Entrar clicado!");
  });

  $(".btn-edit").click(function () {
    alert("¡Botón Editar clicado!");
  });

  $(".input-fill input")
    .each(function () {
      checkForInput(this);
    })
    .on("input", function () {
      checkForInput(this);
    });

  function checkForInput(element) {
    var $label = $(element).next(".input-label");
    if ($(element).val().length > 0) {
      $label.addClass("has-content");
    } else {
      $label.removeClass("has-content");
    }
  }
  $('.crear').click(function() {
    $('#modalAñadirUsuario').show();
});

$('.modal .close').click(function() {
    $('.modal').hide();
});

// Opcional: Cerrar el modal al hacer clic fuera de su contenido
$(window).click(function(e) {
    if ($(e.target).is('.modal')) {
        $('.modal').hide();
    }
});

//   var modal = $("#modalAñadirPlato");
//   var btn = $(".crear");
//   var span = $(".close");

//   btn.on('click', function() {
//       modal.fadeIn(); // o modal.show();
//   });

//   span.on('click', function() {
//       modal.fadeOut(); // o modal.hide();
//   });

//   // Cerrar el modal al hacer clic fuera de él
//   $(window).on('click', function(event) {
//       if ($(event.target).is(modal)) {
//           modal.fadeOut(); // o modal.hide();
//       }
//   });
loadDishes();

// Manejar el evento de clic en el menú
$('.opcion-menu a').on('click', function(event) {
    event.preventDefault();
    var type = $(this).data('type');
    filterDishes(type);
});
});

// Función para cargar todos los platos
function loadDishes() {
  console.log("----");
  $.ajax({
      url: '/servicios_rest/dishes',
      method: 'GET',
      success: function(data) {
          displayDishes(data);
      },
      error: function(err) {
          console.log('Error:', err);
      }
  });
}

// Función para mostrar platos en el DOM
function displayDishes(dishes) {
  let output = '';
  $.each(dishes, function(index, dish) {
      output += `<div class="dish" data-type="${dish.food_type}">
                     <h3>${dish.plate_name}</h3>
                     <p>${dish.descrip}</p>
                     <p>Precio: ${dish.price}</p>
                 </div>`;
  });
  $('#menu').html(output);
}

// Función para filtrar platos por tipo
function filterDishes(type) {
  $('.dish').hide();
  $(`.dish[data-type="${type}"]`).show();
}
