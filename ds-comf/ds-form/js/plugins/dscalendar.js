

function dscalendar(field, opts){
    
            // setting date first time
            var currdate = new Date(),
               defaults = {
                  udate: false,
                  format: '{DD}.{MM}.{YYYY}',
                  multiple: false,  //  format num or 
                  closeOnChoose: true,
                  days: ['пн','вт','ср','чт','пт','сб','вс'],
                  months: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
                  months2: ['Января','Февраля','Марта','Апреля','Мая','Июня','Июля','Августа','Сентября','Октября','Ноября','Декабря'],
                  sundayFirst: false, 
                  onChoose: undefined,
                  onLoad: undefined,       
               };
               
            var today = new Date(currdate.getFullYear(),currdate.getMonth(),currdate.getDate()),
                mtype = 'normal'
                dates = [];
            
            if (typeof opts == 'string') opts = {udate: opts}; 

            if (typeof opts == 'object'){
                for (var opt in defaults) {
                   if (opts[opt] === undefined) {
                      opts[opt] = defaults[opt];
                   }      
                }
            } else {
                opts = defaults;
            }
            
            if (typeof opts.multiple == 'number' && Number.isFinite(opts.multiple) && !(opts.multiple % 1) && opts.multiple > 1){
                mtype = 'shift';
            } else if(Object.prototype.toString.call(opts.multiple) === '[object Array]' && opts.multiple.length > 0){
                mtype = 'custom';
            }
            
            
            var overlook = function (u) {
                if (u && typeof u === 'string'){
                   u = u.split('.');
                   var theYear = parseInt(u[1])
                   var theMonth = parseInt(u[0]) - 1;
                   var theDay = currdate.getDate();
                   currdate = new Date(theYear,theMonth,theDay); 
                } else if (u && typeof u === 'number'){
                   currdate = new Date(currdate.getFullYear(),currdate.getMonth() + u,currdate.getDate());
                }
            }
            overlook(opts.udate);
            var sp = document.createElement('span');
            sp.className = 'dsform-cal-wrapper';
            
            var obj = document.createElement('div');
            obj.className = 'dsform-cal-layer';
            var btn = document.createElement('span');
            btn.className = "dsform-cal-btn";
            field.parentElement.appendChild(btn);
            
            btn.addEventListener('click', function () {
                
               if (btn.className.indexOf('cal-chosen') > -1 ){
                  var cln = new RegExp('cal-chosen', 'g');         
                  btn.className = btn.className.replace(cln, '');
               } else {
                  var space =  btn.className == '' ? '' : ' ';
                  btn.className = btn.className + space + 'cal-chosen';
               }
               
               if (sp.style.display == 'none') {
                  sp.style.display = 'inline';
               } else {
                  sp.style.display = 'none';
               }                  
            
            });
            
            sp.style.display = 'none';
            sp.appendChild(obj);
            field.parentElement.appendChild(sp);
            
               
            function set(d) {
               d.day = opts.sundayFirst == true ? d.getDay() : d.getDay() == 0 ? 6 : d.getDay() - 1;
               d.date = d.getDate();
               d.month = d.getMonth();
               d.monthName = opts.months[d.month];
               d.monthName2 = opts.months2[d.month];
               d.year = d.getFullYear();
               d.nextMonth = new Date(d.year, d.month + 1, d.date);
               d.prevMonth = new Date(d.year, d.month - 1, d.date);
               d.nextMonthName = opts.months[d.nextMonth.getMonth()];
               d.prevMonthName = opts.months[d.prevMonth.getMonth()];
               d.days = Math.ceil((d.nextMonth - d)/86400000);
               var first = new Date(d.year, d.month, 1);
               var last  = new Date(d.year, d.month, d.days);
               d.firstday = opts.sundayFirst == true ? first.getDay() : first.getDay() == 0 ? 6 : first.getDay() - 1;
               d.lastday  = opts.sundayFirst == true ? last.getDay() : last.getDay() == 0 ? 6 : last.getDay() - 1;
               currdate = d;
            } 
            
            var getP = function () {
               set(currdate.prevMonth);
               insert();
            }
            
            var getN = function () {
               set(currdate.nextMonth);
               insert();
            }
            
            var format = function (str, dd, curr) {
               var DD = dd < 10 ? '0' + dd : dd;
               var D = dd;
               var M = curr.month + 1;
               var MM = M < 10 ? '0' + M : M;
               var MMM = opts.months[curr.month].slice(0,3).toLowerCase();
               var MMMM = opts.months[curr.month];
               var MMMMM = opts.months2[curr.month];
               var YY = String(curr.year).slice(2,4);
               var YYYY = curr.year;
               var r = str.replace(/{D}/g,D).replace(/{DD}/g,DD).replace(/{M}/g,M).replace(/{MM}/g,MM).replace(/{MMM}/g,MMM).replace(/{MMMM}/g,MMMM).replace(/{MMMMM}/g,MMMMM).replace(/{YY}/g,YY).replace(/{YYYY}/g,YYYY);
               return r;
            } 
                 
            var dayTD = function (tdo, date, curr) {

                tdo.addEventListener('click', function () {
                  field.value = format(opts.format, date, curr);
                  
                  if (typeof opts.onChoose == 'function'){
                    var r = {
                        d: format('{D}', date, curr),
                        dd: format('{DD}', date, curr),
                        m: format('{M}', date, curr),
                        mm: format('{MM}', date, curr),
                        mmm: format('{MMM}', date, curr),
                        mmmm: format('{MMMM}', date, curr),
                        mmmmm: format('{MMMMM}', date, curr),
                        yy: format('{YY}', date, curr),
                        yyyy: format('{YYYY}', date, curr),
                        format: format(opts.format, date, curr)            
                    };    
                    opts.onChoose.apply(sp, [r]);            
                  }
                  
                  if (opts.closeOnChoose) {
                      
                      if (btn.className.indexOf('cal-chosen') > -1 ){
                         var cln = new RegExp('cal-chosen', 'g');         
                         btn.className = btn.className.replace(cln, '');
                      } else {
                         var space =  btn.className == '' ? '' : ' ';
                         btn.className = btn.className + space + 'cal-chosen';
                      }
                      
                      if (sp.style.display == 'none') {
                         obj.style.display = 'inline';
                      } else {
                         sp.style.display = 'none';
                      }  
                   }
                });
                
                return tdo;
            }
            
            var insert = function (){
               var thismonth = {};

               for (var key in currdate) {
                 thismonth[key] = currdate[key];
               }
               dates.push(thismonth);
               var table = document.createElement('table'),
               current = document.createElement('thead'),
               daystr = document.createElement('tr'),
               monthtr = document.createElement('tr'),
               monthtd = document.createElement('td'),
               weeks = document.createElement('tbody'), 
               daysarr = [];
               
               monthtd.setAttribute('colspan','7');
               monthtd.className = 'mth-hdr';
               monthtd.innerHTML = opts.months[thismonth.month] + ' ' + thismonth.year;
               monthtr.appendChild(monthtd);
               
               if (mtype == 'normal'){
                   var nm = document.createElement('span'),
                   pm = document.createElement('span');
                   
                   nm.className = 'next-month month-btn';
                   pm.className = 'prev-month month-btn';
                   
                   nm.addEventListener('click', getN);
                   pm.addEventListener('click', getP);

                   monthtd.appendChild(pm);
                   monthtd.appendChild(nm);
               }
               
               for (var n = 0; n < 7; n++) {
                  var wda = document.createElement('td');
                  wda.innerHTML = opts.days[n];
                  wda.setAttribute('class','wday wda-' + n);
                  daystr.appendChild(wda);
               }      
               
               
               current.appendChild(monthtr);
               current.appendChild(daystr);
               
               for (var n = - thismonth.firstday+1; n < 43; n++) {
                  daysarr.push(n);
               }
               for (i = 0; i < 7; i++) {
                var tr = document.createElement('tr');
                weeks.appendChild(tr);
               }
               // counting days
               for (var i = 0; i < 42; i++){
                  var date = daysarr[i] < 1 || daysarr[i] > thismonth.days ? '&nbsp;' : daysarr[i];
                  var td = document.createElement('td');
                  if (daysarr[i] == thismonth.date && thismonth.month == today.getMonth() && thismonth.year == today.getFullYear()) td.className = 'crrnt-day';
                  if (date == '&nbsp;') td.className = 'nodate';
                  if (date != '&nbsp;') {
                     td = dayTD(td, date, thismonth);
                  }
                  var week = Math.floor(i/7);
                  td.innerHTML = date;
                  weeks.children[week].appendChild(td);
               } 
               table.className = 'dscalendar-table'
               table.appendChild(current);
               table.appendChild(weeks); 
               if (mtype == 'normal') {
                   while (obj.firstChild) obj.removeChild(obj.firstChild);
               }
               obj.appendChild(table);
            }
            
            set(currdate);
            
            
            if (mtype == 'shift'){
                insert();
                for (var i = 0; i < (opts.multiple - 1); i++) {
                    getN();
                }
            } else if(mtype == 'custom'){
                for (var i = 0; i < (opts.multiple.length); i++) {
                    set(today);
                    overlook(opts.multiple[i]);
                    set(currdate)
                    insert();
                }
            } else {
                insert();
            }
            
            if (typeof opts.onLoad === 'function'){
                var c = currdate;
                opts.onLoad.apply(sp, dates);
            }
                      
         }