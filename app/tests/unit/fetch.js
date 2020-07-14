const fetchPolifill = require('node-fetch');
global.fetch = fetchPolifill.default;
global.Request = fetchPolifill.Request;
global.Headers = fetchPolifill.Headers;
global.Response = fetchPolifill.Response;
