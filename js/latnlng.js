/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
/*  Latitude/longitude spherical geodesy formulae & scripts (c) Chris Veness 2002-2009            */
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */

/*
 * Use Haversine formula to calculate distance (in km) between two points specified by
 * latitude/longitude (in numeric degrees)
 *
 * from: Haversine formula - R. W. Sinnott, "Virtues of the Haversine",
 *       Sky and Telescope, vol 68, no 2, 1984
 *       http://www.census.gov/cgi-bin/geo/gisfaq?Q5.1
 *
 * example usage from form:
 *   result.value = LatLon.distHaversine(lat1.value.parseDeg(), long1.value.parseDeg(),
 *                                       lat2.value.parseDeg(), long2.value.parseDeg());
 * where lat1, long1, lat2, long2, and result are form fields
 */
LatLon.distHaversine = function(lat1, lon1, lat2, lon2) {
  var R = 6371; // earth's mean radius in km
  var dLat = (lat2-lat1).toRad();
  var dLon = (lon2-lon1).toRad();
  lat1 = lat1.toRad(), lat2 = lat2.toRad();

  var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
          Math.cos(lat1) * Math.cos(lat2) *
          Math.sin(dLon/2) * Math.sin(dLon/2);
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
  var d = R * c;
  return d;
}


/*
 * Use Law of Cosines to calculate distance (in km) between two points specified by latitude/longitude
 * (in numeric degrees).
 */
LatLon.distCosineLaw = function(lat1, lon1, lat2, lon2) {
  var R = 6371; // earth's mean radius in km
  var d = Math.acos(Math.sin(lat1.toRad())*Math.sin(lat2.toRad()) +
                    Math.cos(lat1.toRad())*Math.cos(lat2.toRad())*Math.cos((lon2-lon1).toRad())) * R;
  return d;
}


/*
 * calculate (initial) bearing between two points
 *   see http://williams.best.vwh.net/avform.htm#Crs
 */
LatLon.bearing = function(lat1, lon1, lat2, lon2) {
  lat1 = lat1.toRad(); lat2 = lat2.toRad();
  var dLon = (lon2-lon1).toRad();

  var y = Math.sin(dLon) * Math.cos(lat2);
  var x = Math.cos(lat1)*Math.sin(lat2) -
          Math.sin(lat1)*Math.cos(lat2)*Math.cos(dLon);
  return Math.atan2(y, x).toBrng();
}


/*
 * calculate midpoint of great circle line between p1 & p2.
 *   see http://mathforum.org/library/drmath/view/51822.html for derivation
 */
LatLon.midPoint = function(lat1, lon1, lat2, lon2) {
  lat1 = lat1.toRad();
  lat2 = lat2.toRad();
  var dLon = (lon2-lon1).toRad();

  var Bx = Math.cos(lat2) * Math.cos(dLon);
  var By = Math.cos(lat2) * Math.sin(dLon);

  lat3 = Math.atan2(Math.sin(lat1)+Math.sin(lat2),
                    Math.sqrt((Math.cos(lat1)+Bx)*(Math.cos(lat1)+Bx) + By*By ) );
  lon3 = lon1.toRad() + Math.atan2(By, Math.cos(lat1) + Bx);

  if (isNaN(lat3) || isNaN(lon3)) return null;
  return new LatLon(lat3.toDeg(), lon3.toDeg());
}


/*
 * calculate destination point given start point, initial bearing (deg) and distance (km)
 *   see http://williams.best.vwh.net/avform.htm#LL
 */
LatLon.prototype.destPoint = function(brng, d) {
  var R = 6371; // earth's mean radius in km
  var lat1 = this.lat.toRad(), lon1 = this.lon.toRad();
  brng = brng.toRad();

  var lat2 = Math.asin( Math.sin(lat1)*Math.cos(d/R) +
                        Math.cos(lat1)*Math.sin(d/R)*Math.cos(brng) );
  var lon2 = lon1 + Math.atan2(Math.sin(brng)*Math.sin(d/R)*Math.cos(lat1),
                               Math.cos(d/R)-Math.sin(lat1)*Math.sin(lat2));
  lon2 = (lon2+Math.PI)%(2*Math.PI) - Math.PI;  // normalise to -180...+180

  if (isNaN(lat2) || isNaN(lon2)) return null;
  return new LatLon(lat2.toDeg(), lon2.toDeg());
}


/*
 * calculate final bearing arriving at destination point given start point, initial bearing and distance
 */
LatLon.prototype.finalBrng = function(brng, d) {
  var p1 = this, p2 = p1.destPoint(brng, d);
  // get reverse bearing point 2 to point 1
  var rev = LatLon.bearing(p2.lat, p2.lon, p1.lat, p1.lon);
  // & reverse it by adding 180°
  var brng = (rev + 180) % 360;
  return brng;
}


/*
 * calculate distance, bearing, destination point on rhumb line
 *   see http://williams.best.vwh.net/avform.htm#Rhumb
 */
LatLon.distRhumb = function(lat1, lon1, lat2, lon2) {
  var R = 6371; // earth's mean radius in km
  var dLat = (lat2-lat1).toRad(), dLon = Math.abs(lon2-lon1).toRad();
  var dPhi = Math.log(Math.tan(lat2.toRad()/2+Math.PI/4)/Math.tan(lat1.toRad()/2+Math.PI/4));
  var q = (Math.abs(dLat) > 1e-10) ? dLat/dPhi : Math.cos(lat1.toRad());
  // if dLon over 180° take shorter rhumb across 180° meridian:
  if (dLon > Math.PI) dLon = 2*Math.PI - dLon;
  var d = Math.sqrt(dLat*dLat + q*q*dLon*dLon);
  return d * R;
}


LatLon.brngRhumb = function(lat1, lon1, lat2, lon2) {
  var dLon = (lon2-lon1).toRad();
  var dPhi = Math.log(Math.tan(lat2.toRad()/2+Math.PI/4)/Math.tan(lat1.toRad()/2+Math.PI/4));
  if (Math.abs(dLon) > Math.PI) dLon = dLon>0 ? -(2*Math.PI-dLon) : (2*Math.PI+dLon);
  return Math.atan2(dLon, dPhi).toBrng();
}


LatLon.prototype.destPointRhumb = function(brng, dist) {
  var R = 6371; // earth's mean radius in km
  var d = parseFloat(dist)/R;  // d = angular distance covered on earths surface
  var lat1 = this.lat.toRad(), lon1 = this.lon.toRad();
  brng = brng.toRad();

  var lat2 = lat1 + d*Math.cos(brng);
  var dLat = lat2-lat1;
  var dPhi = Math.log(Math.tan(lat2/2+Math.PI/4)/Math.tan(lat1/2+Math.PI/4));
  var q = (Math.abs(dLat) > 1e-10) ? dLat/dPhi : Math.cos(lat1);
  var dLon = d*Math.sin(brng)/q;
  // check for some daft bugger going past the pole
  if (Math.abs(lat2) > Math.PI/2) lat2 = lat2>0 ? Math.PI-lat2 : -(Math.PI-lat2);
  lon2 = (lon1+dLon+Math.PI)%(2*Math.PI) - Math.PI;

  if (isNaN(lat2) || isNaN(lon2)) return null;
  return new LatLon(lat2.toDeg(), lon2.toDeg());
}


/*
 * construct a LatLon object: arguments in numeric degrees
 *
 * note all LatLong methods expect & return numeric degrees (for lat/long & for bearings)
 */
function LatLon(lat, lon) {
  this.lat = lat;
  this.lon = lon;
}


/*
 * represent point {lat, lon} in standard representation
 */
LatLon.prototype.toString = function() {
  return this.lat.toLat() + ', ' + this.lon.toLon();
}

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */

// extend String object with method for parsing degrees or lat/long values to numeric degrees
//
// this is very flexible on formats, allowing signed decimal degrees, or deg-min-sec suffixed by
// compass direction (NSEW). A variety of separators are accepted (eg 3º 37' 09"W) or fixed-width
// format without separators (eg 0033709W). Seconds and minutes may be omitted. (Minimal validation
// is done).

String.prototype.parseDeg = function() {
  if (!isNaN(this)) return Number(this);                 // signed decimal degrees without NSEW

  var degLL = this.replace(/^-/,'').replace(/[NSEW]/i,'');  // strip off any sign or compass dir'n
  var dms = degLL.split(/[^0-9.]+/);                     // split out separate d/m/s
  for (var i in dms) if (dms[i]=='') dms.splice(i,1);    // remove empty elements (see note below)
  switch (dms.length) {                                  // convert to decimal degrees...
    case 3:                                              // interpret 3-part result as d/m/s
      var deg = dms[0]/1 + dms[1]/60 + dms[2]/3600; break;
    case 2:                                              // interpret 2-part result as d/m
      var deg = dms[0]/1 + dms[1]/60; break;
    case 1:                                              // decimal or non-separated dddmmss
      if (/[NS]/i.test(this)) degLL = '0' + degLL;       // - normalise N/S to 3-digit degrees
      var deg = dms[0].slice(0,3)/1 + dms[0].slice(3,5)/60 + dms[0].slice(5)/3600; break;
    default: return NaN;
  }
  if (/^-/.test(this) || /[WS]/i.test(this)) deg = -deg; // take '-', west and south as -ve
  return deg;
}
// note: whitespace at start/end will split() into empty elements (except in IE)


/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */

// extend Number object with methods for converting degrees/radians

Number.prototype.toRad = function() {  // convert degrees to radians
  return this * Math.PI / 180;
}

Number.prototype.toDeg = function() {  // convert radians to degrees (signed)
  return this * 180 / Math.PI;
}

Number.prototype.toBrng = function() {  // convert radians to degrees (as bearing: 0...360)
  return (this.toDeg()+360) % 360;
}


/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */

// extend Number object with methods for presenting bearings & lat/longs

Number.prototype.toDMS = function() {  // convert numeric degrees to deg/min/sec
  var d = Math.abs(this);  // (unsigned result ready for appending compass dir'n)
  d += 1/7200;  // add ½ second for rounding
  var deg = Math.floor(d);
  var min = Math.floor((d-deg)*60);
  var sec = Math.floor((d-deg-min/60)*3600);
  // add leading zeros if required
  if (deg<100) deg = '0' + deg; if (deg<10) deg = '0' + deg;
  if (min<10) min = '0' + min;
  if (sec<10) sec = '0' + sec;
  return deg + '\u00B0' + min + '\u2032' + sec + '\u2033';
}

Number.prototype.toLat = function() {  // convert numeric degrees to deg/min/sec latitude
  return this.toDMS().slice(1) + (this<0 ? 'S' : 'N');  // knock off initial '0' for lat!
}

Number.prototype.toLon = function() {  // convert numeric degrees to deg/min/sec longitude
  return this.toDMS() + (this>0 ? 'E' : 'W');
}

Number.prototype.toPrecision = function(fig) {  // override toPrecision method with one which displays
  if (this == 0) return 0;                      // trailing zeros in place of exponential notation
  var scale = Math.ceil(Math.log(this)*Math.LOG10E);
  var mult = Math.pow(10, fig-scale);
  return Math.round(this*mult)/mult;
}

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */