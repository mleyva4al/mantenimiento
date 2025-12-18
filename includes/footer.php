</div> <!-- Cierre del .container (viene del header.php) -->

    <footer>
        <p>&copy; <?php echo date('Y'); ?> - Sistema de Mantenimiento UdeC</p>
    </footer>

    <!-- jQuery (requerido por DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    
    <!-- JS de DataTables -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
    
    <!-- Librerías de Botones (Exportar) -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <!-- Tu JS personalizado (para la API de modelos, etc.) -->
    <script src="public/js/app.js"></script>

    <!-- 
        *** ¡SCRIPT GLOBAL DE DATATABLES! ***
        Este script ahora maneja TODAS las tablas del sitio.
    -->
    <script>
    $(document).ready(function() {
        // Buscamos cualquier tabla con la clase 'display'
        $('table.display').each(function() {
            
            let defaultOrder = [[ 0, "desc" ]]; // Orden por defecto para logs (Fecha más reciente)

            // Si la tabla es la de usuarios, ordenamos por Nombre (Col 0) Ascendente
            if ($(this).is('#usersTable')) { 
                defaultOrder = [[ 0, "asc" ]]; // Ordena por Nombre A-Z
            } 
            // Si la tabla es logsTable (usada en reporte_sesiones) o logsAccionesTable (usada en reporte_acciones), 
            // mantenemos el orden por defecto: Col 0 (Fecha) descendente.
            else if ($(this).is('#logsTable')) {
                defaultOrder = [[ 0, "desc" ]];
            }
            
            // Verificamos si la tabla ya fue inicializada para evitar el error "Cannot reinitialise DataTable"
            if ($.fn.DataTable.isDataTable(this)) {
                // Si ya fue inicializada, la destruimos antes de re-inicializar
                $(this).DataTable().destroy();
            }

            $(this).DataTable({
                dom: 'lBfrtip', // 'l'=Length, 'B'=Buttons, 'f'=Filter, 'r'=Processing, 't'=Table, 'i'=Info, 'p'=Pagination
                buttons: [
                    'copy',  // Botón de Copiar
                    'csv',   // Botón de CSV (Excel)
                    'pdf',   // Botón de PDF
                    'print'  // Botón de Imprimir
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
                },
                responsive: true,
                order: defaultOrder // Aplicamos el orden dinámico
            });
        });

    });
    </script>
</body>
</html>