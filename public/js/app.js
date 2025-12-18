// public/js/app.js
document.addEventListener('DOMContentLoaded', function() {
    
    const marcaSelect = document.getElementById('select_marca');
    const modeloSelect = document.getElementById('select_modelo');

    if (marcaSelect) {
        marcaSelect.addEventListener('change', function() {
            const marcaId = this.value;
            modeloSelect.innerHTML = '<option value="">Cargando...</option>';
            modeloSelect.disabled = true;

            if (!marcaId) {
                modeloSelect.innerHTML = '<option value="">Seleccione una marca primero</option>';
                return;
            }

            // Llamada a nuestra API interna
            fetch(`api_modelos.php?id_marca=${marcaId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta de la API');
                    }
                    return response.json();
                })
                .then(data => {
                    modeloSelect.innerHTML = ''; // Limpiar
                    if (data.length > 0) {
                        modeloSelect.innerHTML = '<option value="">Seleccione un modelo</option>';
                        data.forEach(modelo => {
                            const option = document.createElement('option');
                            option.value = modelo.nombre;
                            option.textContent = modelo.nombre;
                            modeloSelect.appendChild(option);
                        });
                        modeloSelect.disabled = false;
                    } else {
                        modeloSelect.innerHTML = '<option value="">No hay modelos registrados</option>';
                    }
                })
                .catch(error => {
                    console.error('Error al cargar modelos:', error);
                    modeloSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        });
    }
});