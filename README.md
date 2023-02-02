## Invoice generator

For my business I need to send out invoices and those are mostly based on my time registration data. That turned out to be quite time intensive. The first few times I did it by hand using a spreadsheet, but that got old real fast so I took some time to automate it. After a while I thought it might be fun to open source this.

This Laravel based app retrieves all data from my time tracking app [Toggl Track](https://toggl.com/track/) using their [REST api](https://developers.track.toggl.com/) and compiles it into the correct form to create an invoice for [e-boekhouden](https://www.e-boekhouden.nl/) using their [SOAP webservice](https://www.e-boekhouden.nl/koppelingen/api). It creates invoice lines based on time entries grouped by project name, will round the time to quarters and create descriptions including a list of dates.

**Warning**: This is not even alpha software. The core is functional for me (it already saves me a lot of work each month) but it's not ready in any sense. If you're using Toggl Track for time tracking and e-boekhouden for book keeping, and you have a similar work flow as I have, and you feel brave, this software might be useful to you. But some assembly is required. Many features and even basic glue between components are missing. There's no stable API, no documentation, not even a proper frontend yet. Use at your own risk. 
