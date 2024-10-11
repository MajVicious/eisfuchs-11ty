const eleventyNavigationPlugin = require("@11ty/eleventy-navigation");
const fs = require("fs-extra");
const markdownIt = require("markdown-it");
const markdownItAttrs = require("markdown-it-attrs");

const markdownItOptions = {
  html: true,
  breaks: true,
  linkify: true,
};

const markdownLib = markdownIt(markdownItOptions).use(markdownItAttrs);

module.exports = function (eleventyConfig) {
  eleventyConfig.setLibrary("md", markdownLib);
  eleventyConfig.addPlugin(eleventyNavigationPlugin);
  eleventyConfig.addPassthroughCopy("src/img");
  eleventyConfig.addPassthroughCopy("src/php");
  eleventyConfig.addPassthroughCopy({
    "./node_modules/@fortawesome/fontawesome-free/css/all.min.css":
      "assets/fontawesome.css",
  });
  eleventyConfig.addPassthroughCopy({
    "./node_modules/@fortawesome/fontawesome-free/webfonts/": "webfonts/",
  });
  eleventyConfig.addPassthroughCopy({
    "src/favicon/": "/",
  });
  eleventyConfig.addPassthroughCopy({
    "./node_modules/alpinejs/dist/cdn.js": "./assets/alpine.js",
  });

  eleventyConfig.addCollection("footerNav", function (collection) {
    return collection.getAll().filter(function (item) {
      return (
        item.data.eleventyNavigation &&
        item.data.eleventyNavigation.parent === "FooterNav"
      );
    });
  });
  
  const now = String(Date.now());

  eleventyConfig.addShortcode("version", function () {
    return now;
  });

  const { DateTime } = require("luxon");

  // https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#valid-date-string
  eleventyConfig.addFilter("htmlDateString", (dateObj) => {
    return DateTime.fromJSDate(dateObj, {
      zone: "utc",
    }).toFormat("yy-MM-dd");
  });

  eleventyConfig.addFilter("readableDate", (dateObj) => {
    return DateTime.fromJSDate(dateObj, {
      zone: "utc",
    }).toFormat("dd-MM-yy");
  });

  return {
    dir: { input: "src", output: "_site" },
  };
};
