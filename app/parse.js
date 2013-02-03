// Generated by CoffeeScript 1.4.0
(function() {
  var execFile, parse;

  execFile = require("child_process").execFile;

  parse = function(path, cb) {
    return execFile("bin/pdftotext.exe", ["-layout", path, path + ".txt"], function(e, so, se) {
      if (e) {
        return false;
      }
      execFile("php", ["bin/parse.php", "-f" + path + ".txt"], function(pe, pso, pse) {
        if (pe) {
          return false;
        }
        return cb(JSON.parse(pso));
      });
      return true;
    });
  };

  exports.PdfParse = parse;

}).call(this);