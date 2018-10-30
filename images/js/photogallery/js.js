 var myWin;

function openwin(cname,srci,xx,yy)  {
  var img = new Image();
  img.src=srci;
  if (myWin) myWin.close();
  myWin = open("", "displayWindow","width="+(xx)+",height="+yy+",status=no,toolbar=no,menubar=no,scrollbars=0");
  myWin.document.open();
  myWin.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">\n');
  myWin.document.write('<html>\n<head><title>'+cname+'</title></head>\n');
  myWin.document.write('<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0>\n<a href="javascript:window.close();"><img src="'+img.src+'" border="0"></a>\n</body></html>\n');
  myWin.document.close();
}