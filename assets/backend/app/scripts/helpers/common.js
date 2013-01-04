(function() {

  window.serialize = function(obj, prefix) {
    var k, p, str, v, _i, _len;
    str = [];
    for (_i = 0, _len = obj.length; _i < _len; _i++) {
      p = obj[_i];
      k = prefix ? prefix + "[" + p + "]" : p;
      v = obj[p];
      str.push(typeof v === "object" ? serialize(v, k) : encodeURIComponent(k) + "=" + encodeURIComponent(v));
    }
    return str.join("&");
  };

}).call(this);