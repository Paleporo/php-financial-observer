# PHP Financial Observer MCP Server

Un micro server MCP in PHP 8.4 che espone endpoint REST per monitorare ETF e mercato azionario tramite le API pubbliche di Financial Modeling Prep (piano free/demo). Può essere collegato a ChatGPT come tool HTTP o server MCP locale.

## Requisiti
- PHP 8.4+
- Nessuna dipendenza esterna: usa l'autoloader incluso in `bootstrap.php`.

## Configurazione
Imposta la chiave API (opzionale, default `demo` offerta gratuitamente da FMP):

```bash
export FMP_API_KEY="la-tua-api-key"
```

## Avvio
Esegui il server di sviluppo PHP puntando alla cartella `public`:

```bash
php -S 0.0.0.0:8080 -t public
```

### Endpoint
- `GET /health`: stato del server MCP.
- `GET /api/markets/overview`: panoramica di indici azionari globali (S&P 500, Dow Jones, Nasdaq, Russell 2000, FTSE 100, DAX, Nikkei 225).
- `GET /api/etf/top-movers?limit=10`: lista dei principali ETF (limit fino a 100).
- `GET /api/quotes/{symbol}`: dati di un titolo/ETF specifico tramite simbolo (es. `AAPL`, `SPY`).

Lo schema OpenAPI (`openapi.yaml`) documenta parametri e payload di risposta degli endpoint.

Le risposte sono sempre JSON con chiave `data` o `error`.

## Note sul collegamento a ChatGPT
- Configura l'azione o il tool HTTP per chiamare l'host/porta del server.
- Gli endpoint non richiedono autenticazione aggiuntiva; se serve, passa l'header `Authorization` a monte.
- In caso di rate limit dell'API pubblica, l'endpoint restituirà un payload con `{"error": "..."}` utile per il debug.
