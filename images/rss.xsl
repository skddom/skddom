<?xml version="1.0" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="html"/>
<xsl:template match="/rss/channel">
<html>
<head>
<title><xsl:value-of select="title"/></title>
<style>
h1, h1 a {
  background:#A00;
  color:#FFF;
  padding:10px;
  text-decoration:none;
}
a {
  color:#A00;
}
dd {
  font-size:80%;
  margin:10px;
}
dt {
  padding:5px;
  margin:0 10px;
  font-weight:bold
}
body {
  font-family:tahoma,arial,verdana,sans-serif;
  margin:0;
}
</style>
</head>

<body>
<h1>
  <a>
    <xsl:attribute name="href"><xsl:value-of select="link"/></xsl:attribute>
    <xsl:value-of select="title"/>
  </a>
</h1>

<dl>
<xsl:for-each select="item">
  <dt>
    <a>
      <xsl:attribute name="href"><xsl:value-of select="link"/></xsl:attribute>
      <span name="decodeable">
        <xsl:value-of select="title" disable-output-escaping = "yes"/>
      </span>
    </a>
  </dt>
  <dd>
    <span name="decodeable">
      <xsl:value-of select="description" disable-output-escaping = "yes"/>
    </span>
    <br />
    <span>
      <xsl:attribute name="style">font-size:0.8em;color:#555</xsl:attribute>
      <xsl:value-of select="pubDate"/>
    </span>
  </dd>
</xsl:for-each>
</dl>

</body>
</html>
</xsl:template>
</xsl:stylesheet> 