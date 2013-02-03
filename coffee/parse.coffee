execFile = require("child_process").execFile

parse = (path, cb) ->
  execFile "bin/pdftotext.exe", ["-layout", path, path + ".txt"], (e, so, se) ->
    return false if e

    execFile "php", ["bin/parse.php", "-f" + path + ".txt"], (pe, pso, pse) ->
        return false if pe

        cb(JSON.parse(pso))

    return true

exports.PdfParse = parse