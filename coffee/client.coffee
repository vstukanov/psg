pdf_parse = require("./app/parse.js").PdfParse

$drop_file = $ "#drop-file"
drop_file_node = $drop_file[0]

div = "<div class='foo'></div>"

noop_func = (e) ->
  e.stopPropagation()
  e.preventDefault()

drop_file_node.addEventListener "dragenter", noop_func, false
drop_file_node.addEventListener "dragexit", noop_func, false
drop_file_node.addEventListener "dragover", noop_func, false
drop_file_node.addEventListener "drop", ((ev) ->
  ev.stopPropagation()
  ev.preventDefault()

  _(ev.dataTransfer.files).each (v) ->
    pdf_parse v.path.replace(/\\/g, "\\\\"), (data) ->
      console.log data

  ), false