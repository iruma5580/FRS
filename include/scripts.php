

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<!-- <script src="dist/js/demo.js"></script> -->
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>

<script>
    // Toggle password visibility
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.innerHTML = `
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                      stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="3"
                        stroke-linecap="round" stroke-linejoin="round"/>
            `;
        } else {
            passwordInput.type = 'password';
            eyeIcon.innerHTML = `
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                      stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="3"
                        stroke-linecap="round" stroke-linejoin="round"/>
                <line x1="12" y1="12" x2="12" y2="12"
                      stroke-linecap="round" stroke-linejoin="round"/>
            `;
        }
    }

    // Auto-focus first input on load
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('username').focus();
    });
</script>

<script>
  const logoutBtn = document.getElementById('logoutBtn');
  const modal = document.getElementById('logoutModal');
  const confirmBtn = document.getElementById('confirmLogout');
  const cancelBtn = document.getElementById('cancelLogout');
  const logoutForm = document.getElementById('logoutForm');

  // Show modal on logout button click
  logoutBtn.addEventListener('click', () => {
    modal.setAttribute('aria-hidden', 'false');
    modal.focus();
  });

  // Confirm logout: submit hidden form
  confirmBtn.addEventListener('click', () => {
    modal.setAttribute('aria-hidden', 'true');
    logoutForm.submit();
  });

  // Cancel logout: hide modal
  cancelBtn.addEventListener('click', () => {
    modal.setAttribute('aria-hidden', 'true');
  });

  // Close modal on Escape key
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') {
      modal.setAttribute('aria-hidden', 'true');
    }
  });

  // Trap focus inside modal when open (basic)
  modal.addEventListener('keydown', (e) => {
    if (e.key === 'Tab') {
      const focusable = modal.querySelectorAll('button');
      const first = focusable[0];
      const last = focusable[focusable.length - 1];

      if (e.shiftKey) {
        if (document.activeElement === first) {
          e.preventDefault();
          last.focus();
        }
      } else {
        if (document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      }
    }
  });
</script>

<script>
    // Toggle password visibility
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.innerHTML = `
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                      stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="3"
                        stroke-linecap="round" stroke-linejoin="round"/>
            `;
        } else {
            passwordInput.type = 'password';
            eyeIcon.innerHTML = `
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                      stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="3"
                        stroke-linecap="round" stroke-linejoin="round"/>
                <line x1="12" y1="12" x2="12" y2="12"
                      stroke-linecap="round" stroke-linejoin="round"/>
            `;
        }
    }

    // Auto-focus first input on load
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('username').focus();
    });
</script>

<script>
    // Toggle confirm_password visibility
    function togglePasswords() {
        const passwordInput = document.getElementById('confirm_password');
        const eyeIcon = document.getElementById('eye-icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.innerHTML = `
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                      stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="3"
                        stroke-linecap="round" stroke-linejoin="round"/>
            `;
        } else {
            passwordInput.type = 'password';
            eyeIcon.innerHTML = `
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                      stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="3"
                        stroke-linecap="round" stroke-linejoin="round"/>
            `;
        }
    }

    // Auto-focus first input on load
    document.addEventListener('DOMContentLoaded', () => {
        const usernameInput = document.getElementById('username');
        if (usernameInput) {
            usernameInput.focus();
        }
    });
</script>

<!-- DataTables  & Plugins -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<!-- Page specific script -->
<script>
  $(function () {
    // $("#example1").DataTable({
    //   "responsive": true, "lengthChange": false, "autoWidth": false,
    //   "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    // }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });

    // $('#account2').DataTable({
    //   paging: true,
    //   lengthChange: false,
    //   searching: true,
    //   ordering: true,
    //   info: true,
    //   autoWidth: false,
    //   responsive: true,
    //   buttons: [
    //     {
    //       extend: 'collection',
    //       text: 'Export',
    //       buttons: [
    //         'copy',
    //         'csv',
    //         'excel',
    //         'pdf',
    //         {
    //           extend: 'print',
    //           exportOptions: {
    //             columns: [0, 1, 2, 3, 4, 5, 6] // Only print these columns by index
    //           },
    //           customize: function (win) {
    //             // Remove the action buttons column header (assuming last column)
    //             $(win.document.body).find('table thead tr th:last-child').remove();
    //             // Remove the action buttons cells in each row (last column)
    //             $(win.document.body).find('table tbody tr').each(function () {
    //               $(this).find('td:last-child').remove();
    //             });
    //           }
    //         }
    //       ]
    //     }
    //   ]
    // }).buttons().container().appendTo('#account2_wrapper .col-md-6:eq(0)');


    $("#account2").DataTable({
  paging: true,
  lengthChange: false,
  searching: true,
  ordering: true,
  info: true,
  autoWidth: false,
  responsive: true,
  buttons: [
    {
      extend: 'collection',
      text: 'Export',
      buttons: [
        {
          extend: 'copyHtml5',
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6]
          }
        },
        {
          extend: 'csvHtml5',
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6]
          }
        },
        {
          extend: 'excelHtml5',
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6]
          }
        },
        {
          extend: 'pdfHtml5',
          orientation: 'landscape',
          pageSize: 'A4',
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6]
          },
          customize: function (doc) {
            doc.pageMargins = [20, 20, 20, 20];
            doc.defaultStyle.fontSize = 8;
            doc.styles.tableHeader.fontSize = 9;
          }
        },
        {
          extend: 'print',
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6]
          },
          customize: function (win) {
            $(win.document.body).find('table thead tr th:last-child').remove();
            $(win.document.body).find('table tbody tr').each(function () {
              $(this).find('td:last-child').remove();
            });
          }
        }
      ]
    }
  ]
}).buttons().container().appendTo('#account2_wrapper .col-md-6:eq(0)');


    $('#inventoryTable').DataTable({
      buttons: [
        {
          extend: 'collection',
          text: 'Export',
          buttons: [
            'copy',
            'csv',
            'excel',
            'pdf',
            {
              extend: 'print',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6]
              },
              customize: function (win) {
                $(win.document.body).find('table thead tr th:last-child').remove();
                $(win.document.body).find('table tbody tr').each(function () {
                  $(this).find('td:last-child').remove();
                });
              }
            }
          ]
        }
      ]

    }).buttons().container().appendTo('#inventoryTable_wrapper .col-md-6:eq(0)');

  });
</script>

<script>
  $(function () {
    $("#example1").DataTable({
      responsive: true,
      lengthChange: false,
      autoWidth: false,
      buttons: [
        "copy",
        "csv",
        "excel",
        "pdf",
        {
          extend: "print",

          exportOptions: {
          columns: [0, 1, 2,3,4,5,6,7,8]  // Only print these columns by index
          },

          customize: function (win) {
            // Remove the action buttons column header (assuming last column)
            $(win.document.body).find('table thead tr th:last-child').remove();
            // Remove the action buttons cells in each row (last column)
            $(win.document.body).find('table tbody tr').each(function () {
              $(this).find('td:last-child').remove();
            });
          }
          
        },
        "colvis"
      ]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    // $("#workTable").DataTable({
    //   responsive: true,
    //   lengthChange: false,
    //   autoWidth: false,
    //   buttons: [
    //     {
    //       extend: 'collection',
    //       text: 'Export',
    //       buttons: [
    //         'copyHtml5',
    //         'csvHtml5',
    //         'excelHtml5',
    //         'pdfHtml5',
    //         {
    //           extend: 'print',
    //           exportOptions: {
    //             columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11] // Only print these columns by index
    //           },
    //           customize: function (win) {
    //             // Remove the action buttons column header (assuming last column)
    //             $(win.document.body).find('table thead tr th:last-child').remove();
    //             // Remove the action buttons cells in each row (last column)
    //             $(win.document.body).find('table tbody tr').each(function () {
    //               $(this).find('td:last-child').remove();
    //             });
    //           }
    //         }
    //       ]
    //     }
    //   ]
    // }).buttons().container().appendTo('#workTable_wrapper .col-md-6:eq(0)');


    $("#workTable").DataTable({
      responsive: true,
      lengthChange: false,
      autoWidth: false,
      buttons: [
        {
          extend: 'collection',
          text: 'Export',
          buttons: [
            {
              extend: 'copyHtml5',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
              }
            },
            {
              extend: 'csvHtml5',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
              }
            },
            {
              extend: 'excelHtml5',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
              }
            },
            {
              extend: 'pdfHtml5',
              orientation: 'landscape',
              pageSize: 'A4',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
              },
              customize: function (doc) {
                doc.pageMargins = [20, 20, 20, 20];
                doc.defaultStyle.fontSize = 8;
                doc.styles.tableHeader.fontSize = 9;
              }
            },
            {
              extend: 'print',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
              },
              customize: function (win) {
                $(win.document.body).find('table thead tr th:last-child').remove();
                $(win.document.body).find('table tbody tr').each(function () {
                  $(this).find('td:last-child').remove();
                });
              }
            }
          ]
        }
      ]
    }).buttons().container().appendTo('#workTable_wrapper .col-md-6:eq(0)');



    $("#inventoryTable2").DataTable({
      responsive: true,
      lengthChange: false,
      autoWidth: false,
      buttons: [
        {
          extend: 'collection',
          text: 'Export',
          buttons: [
            {
              extend: 'copyHtml5',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
              }
            },
            {
              extend: 'csvHtml5',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
              }
            },
            {
              extend: 'excelHtml5',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
              }
            },
            {
              extend: 'pdfHtml5',
              orientation: 'landscape',
              pageSize: 'A4',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
              }
            },
            {
              extend: 'print',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
              },
              customize: function (win) {
                $(win.document.body).find('table thead tr th:last-child').remove();
                $(win.document.body).find('table tbody tr').each(function () {
                  $(this).find('td:last-child').remove();
                });
              }
            }
          ]
        }
      ]
    }).buttons().container().appendTo('#inventoryTable2_wrapper .col-md-6:eq(0)');


    // $("#inventoryTable2").DataTable({
    //   responsive: true,
    //   lengthChange: false,
    //   autoWidth: false,
    //   buttons: [
    //     {
    //       extend: 'collection',
    //       text: 'Export',
    //       buttons: [
    //         'copyHtml5',
    //         'csvHtml5',
    //         'excelHtml5',
    //         'pdfHtml5',
    //         {
    //           extend: 'print',
    //           exportOptions: {
    //             columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] // Only print these columns by index
    //           },
    //           customize: function (win) {
    //             // Remove the action buttons column header (assuming last column)
    //             $(win.document.body).find('table thead tr th:last-child').remove();
    //             // Remove the action buttons cells in each row (last column)
    //             $(win.document.body).find('table tbody tr').each(function () {
    //               $(this).find('td:last-child').remove();
    //             });
    //           }
    //         }
    //       ]
    //     }
    //   ]
    // }).buttons().container().appendTo('#inventoryTable2_wrapper .col-md-6:eq(0)');


    $("#dataList").DataTable({
      responsive: true,
      lengthChange: false,
      autoWidth: false,
      
      buttons: [
        "copy",
        "csv",
        "excel",
        "pdf",
        {
          extend: "print",

          exportOptions: {
          columns: [0, 1, 2,3,4,5,6,7,8,9,10,11]  // Only print these columns by index
          },

          customize: function (win) {
            // Remove the action buttons column header (assuming last column)
            $(win.document.body).find('table thead tr th:last-child').remove();
            // Remove the action buttons cells in each row (last column)
            $(win.document.body).find('table tbody tr').each(function () {
              $(this).find('td:last-child').remove();
            });
          }
          
        },
        "colvis"
      ]
    }).buttons().container().appendTo('#dataList_wrapper .col-md-6:eq(0)');

  });

  $('#account').DataTable({
    responsive: {
      details: {
        type: 'inline', // or 'column', 'child'
        target: 'td', // where to click to expand
        renderer: function ( api, rowIdx, columns ) {
          // Custom rendering of child row with editing controls
          var data = $.map( columns, function ( col, i ) {
            return col.hidden ?
              '<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                '<td>'+col.title+':'+'</td> '+
                '<td>'+col.data+'</td>'+
              '</tr>' :
              '';
          } ).join('');
          return data ? $('<table/>').append( data ) : false;
        }
      }
    },
    // other options...
  });
</script>

<script>
      (function () {
        const searchInput = document.getElementById('example1');
        const table = document.getElementById('example1');
        const printBtn = document.getElementById('printBtn');

        if (!searchInput || !table || !printBtn) return;

        // Filter table rows based on search input
        searchInput.addEventListener('input', function() {
          const filter = this.value.toLowerCase();
          const rows = table.tBodies[0].rows;

          for (let row of rows) {
            const text = Array.from(row.cells).map(td => td.textContent.toLowerCase()).join(' ');
            row.style.display = text.includes(filter) ? '' : 'none';
          }
        });

        // Helper to wait for images to load in a document
        function waitForImages(doc) {
          const imgs = Array.from(doc.images || []);
          if (!imgs.length) return Promise.resolve();

          return Promise.all(imgs.map(img => {
            if (img.complete && img.naturalWidth > 0) return Promise.resolve();

            return new Promise(resolve => {
              const done = () => resolve();
              img.addEventListener('load', done, { once: true });
              img.addEventListener('error', done, { once: true });
            });
          }));
        }

        // Print button click handler
        printBtn.addEventListener('click', async function () {
          // Clone the table
          const clone = table.cloneNode(true);

          // Remove Action column from clone
          const ths = Array.from(clone.querySelectorAll('thead th'));
          const actionIndex = ths.findIndex(th => (th.textContent || '').trim().toLowerCase() === 'actions');
          if (actionIndex >= 0) {
            ths[actionIndex].remove();
            clone.querySelectorAll('tbody tr').forEach(tr => {
              const tds = tr.querySelectorAll('td');
              if (tds[actionIndex]) tds[actionIndex].remove();
            });
          }

          // Remove rows hidden in original table (filtered out)
          const originalRows = Array.from(table.tBodies[0].rows);
          const cloneRows = Array.from(clone.tBodies[0].rows);

          cloneRows.forEach((cloneRow, i) => {
            if (originalRows[i].style.display === 'none') {
              cloneRow.remove();
            }
          });

          // Open print window
          const w = window.open('', '', 'width=1000,height=700');
          if (!w) {
            alert('Popup blocked. Please allow popups to print.');
            return;
          }

          const title = 'Assets Inventory';
          const now = new Date();
          const stamp = now.toLocaleString();

          w.document.open();
          w.document.write(`
            <!doctype html>
            <html>
            <head>
              <meta charset="utf-8" />
              <meta name="viewport" content="width=device-width, initial-scale=1" />
              <title>${title}</title>
              <style>
                :root { --ink:#0b1220; --muted:#5b6475; --line:#d7dbe3; }
                * { box-sizing: border-box; }
                body {
                  margin: 28px;
                  font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
                  color: var(--ink);
                }
                header {
                  display: flex;
                  align-items: baseline;
                  justify-content: space-between;
                  gap: 16px;
                  margin-bottom: 14px;
                  padding-bottom: 12px;
                  border-bottom: 2px solid var(--ink);
                }
                h1 {
                  margin: 0;
                  font-size: 20px;
                  letter-spacing: .2px;
                }
                .meta {
                  font-size: 12px;
                  color: var(--muted);
                  white-space: nowrap;
                }
                table {
                  width: 100%;
                  border-collapse: collapse;
                  table-layout: fixed;
                }
                thead th {
                  text-align: left;
                  font-size: 12px;
                  letter-spacing: .04em;
                  text-transform: uppercase;
                  padding: 10px 8px;
                  border-bottom: 1px solid var(--ink);
                }
                tbody td {
                  vertical-align: top;
                  padding: 10px 8px;
                  border-bottom: 1px solid var(--line);
                  font-size: 13px;
                  word-wrap: break-word;
                }
                .text-center { text-align: center; }
                img.asset-qr {
                  display: inline-block;
                  width: 92px;
                  height: 92px;
                  image-rendering: pixelated;
                }
                .small {
                  font-size: 11px;
                  color: var(--muted);
                  margin-top: 4px;
                }
                @media print {
                  body { margin: 14mm; }
                  header { border-bottom: 1px solid #000; }
                  thead { display: table-header-group; }
                  tr { break-inside: avoid; page-break-inside: avoid; }
                }
              </style>
            </head>
            <body>
              <header>
                <h1>${title}</h1>
                <div class="meta">Printed: ${stamp}</div>
              </header>
              ${clone.outerHTML}
            </body>
            </html>
          `);
          w.document.close();

          await new Promise(resolve => {
            w.addEventListener('load', resolve, { once: true });
            setTimeout(resolve, 400);
          });

          await waitForImages(w.document);

          w.focus();
          w.print();
          w.close();
        });
      })();

</script>


<script>
var confirmDeleteModal = document.getElementById('confirmDeleteModal');
confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
  var button = event.relatedTarget; // Button that triggered the modal
  var itemId = button.getAttribute('data-item-id'); // Extract info from data-* attributes
  var input = confirmDeleteModal.querySelector('input[name="item_id"]');
  input.value = itemId;
});
</script>

