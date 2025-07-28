const express = require("express");
const bodyParser = require("body-parser");
const { Cluster } = require("puppeteer-cluster");
const fs = require("fs");
const path = require("path");

(async () => {
  const app = express();
  app.use(bodyParser.json({ limit: "10mb" }));

  const cluster = await Cluster.launch({
    concurrency: Cluster.CONCURRENCY_CONTEXT,
    maxConcurrency: 4,
    puppeteerOptions: {
      headless: "new",
      args: ["--no-sandbox", "--disable-setuid-sandbox"],
    },
  });

  await cluster.task(async ({ page, data }) => {
    try {
      const { html, outputFilename, paper = {} } = data;

      console.log(`⚙️ Membuat PDF di ${outputFilename}`);

      await page.setContent(html, { waitUntil: "networkidle0" });

      const pdfOptions = {
        path: outputFilename,
        printBackground: true,
      };

      if (paper.format) {
        pdfOptions.format = paper.format;
      } else if (paper.width && paper.height) {
        pdfOptions.width = paper.width;
        pdfOptions.height = paper.height;
      }

      await page.pdf(pdfOptions);

      console.log(`✅ Selesai: ${outputFilename}`);
    } catch (err) {
      console.error("🛑 Gagal membuat PDF:", err);
      throw err;
    }
  });

  app.post("/generate-pdf", async (req, res) => {
    const { html, filename, paper } = req.body;
    const outputDir = path.join(__dirname, "writable/temp/");
    const outputPath = path.join(outputDir, filename);

    try {
      // Buat folder jika belum ada
      if (!fs.existsSync(outputDir)) {
        fs.mkdirSync(outputDir, { recursive: true });
        console.log(`📁 Folder dibuat: ${outputDir}`);
      }

      console.log(`📄 Membuat PDF di: ${outputPath}`);

      await cluster.execute({ html, outputFilename: outputPath, paper });

      if (!fs.existsSync(outputPath)) {
        console.error("❌ PDF tidak ditemukan setelah cluster.execute");
        return res
          .status(500)
          .json({ success: false, error: "PDF tidak berhasil dibuat" });
      }

      res.json({ success: true, file: filename });
    } catch (err) {
      console.error("🔥 Error saat membuat PDF:", err);
      res.status(500).json({ success: false, error: err.message });
    }
  });

  app.listen(3002, () => console.log("PDF Worker listening on port 3002"));
})();
