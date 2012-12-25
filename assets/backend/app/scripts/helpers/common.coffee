window.serialize = (obj, prefix) ->
  str = [];
  for p in obj
    k = if prefix then prefix + "[" + p + "]" else p
    v = obj[p]
    str.push(if typeof v is "object"then serialize(v, k) else
      encodeURIComponent(k) + "=" + encodeURIComponent(v));
  return str.join("&");
