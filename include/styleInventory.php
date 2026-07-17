  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;max-width:1000px;margin:22px auto;background:#f5f6f8;padding:18px}
    h1{margin:0 0 12px}
    .panel{background:#fff;border:1px solid #d7dbe3;border-radius:10px;padding:14px;margin:12px 0}
    label{display:block;margin-top:10px;font-size:13px;color:#2c3440}
    input[type=text], select{width:100%;padding:9px;border:1px solid #cfd6e2;border-radius:8px;margin-top:5px}
    input.error, select.error{border-color:#b00020;background:#fff0f0}
    .error-msg{color:#b00020;font-size:12px;margin-top:4px;}
    button{padding:9px 12px;border-radius:8px;border:1px solid #cfd6e2;background:#0f62fe;color:#fff;cursor:pointer}
    button.secondary{background:#fff;color:#0f62fe}
    button.danger{background:#fff;color:#b00020;border-color:#f0b6bf}
    table{width:100%;border-collapse:collapse;background:#fff;border:1px solid #d7dbe3;border-radius:10px;overflow:hidden}
    th,td{border-bottom:1px solid #eef1f6;padding:10px;text-align:left;vertical-align:top;font-size:14px}
    th{background:#fafbff;font-size:13px;color:#3a4656}
    tr:hover td{background:#fbfcff}
    .actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
    .linkbtn{border:0;background:transparent;color:#0f62fe;cursor:pointer;padding:0;font:inherit}
    .muted{color:#667085;font-size:13px;margin-top:6px}
    .toast{padding:10px 12px;border-radius:10px;border:1px solid #d7dbe3;background:#fff;margin:12px 0}
    .toast.ok{border-color:#b7ebc6;background:#f1fff5}
    .toast.err{border-color:#f0b6bf;background:#fff5f6}

    /* Modal */
    .backdrop{position:fixed;inset:0;background:rgba(15, 23, 42, .55);display:none;align-items:center;justify-content:center;padding:16px}
    .backdrop.open{display:flex}
    .modal{
      width:min(720px, 100%);
      background:#fff;border:1px solid #d7dbe3;border-radius:14px;
      box-shadow:0 30px 80px rgba(2,6,23,.35);
      overflow:hidden
    }
    .modal-hd{display:flex;justify-content:space-between;align-items:center;padding:12px 14px;border-bottom:1px solid #eef1f6}
    .modal-hd strong{font-size:15px}
    .modal-bd{padding:14px}
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    .grid .full{grid-column:1/-1}
    @media (max-width:640px){.grid{grid-template-columns:1fr}}
    .x{border:1px solid #d7dbe3;background:#fff;color:#101828;border-radius:10px;padding:6px 10px;cursor:pointer}
    .modal-ft{display:flex;gap:10px;justify-content:flex-end;align-items:center;padding:12px 14px;border-top:1px solid #eef1f6}
    .qrimg{border:1px solid #eef1f6;border-radius:10px;background:#fff}
  </style>