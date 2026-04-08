UNAME_S := $(shell uname -s)

MERCURE_JWT_SECRET=abcdefghijklmnopqrstuvwxyz123456 # Le code doit correspondre à celui dans le .env et doit être suffisamment long (256 bits : >= 32 caractères)
MERCURE_CMD=./bin/mercure
CADDYFILE=./bin/dev.Caddyfile

start:
	@echo "🚀 Starting Symfony..."
	symfony server:start --no-tls

	@echo "🟡 Starting Mercure..."

ifeq ($(OS),Windows_NT)	# Commande Windows
	set MERCURE_PUBLISHER_JWT_KEY=$(MERCURE_JWT_SECRET) && \
	set MERCURE_SUBSCRIBER_JWT_KEY=$(MERCURE_JWT_SECRET) && \
	$(MERCURE_CMD) run --config $(CADDYFILE)
else
	MERCURE_PUBLISHER_JWT_KEY=$(MERCURE_JWT_SECRET) MERCURE_SUBSCRIBER_JWT_KEY=$(MERCURE_JWT_SECRET) $(MERCURE_CMD) run --config $(CADDYFILE) > mercure.log 2>&1 &
endif

stop:
	symfony server:stop
	taskkill //IM mercure.exe //F || true # Si la commande échoue, makefile ignore l'erreur et ne s'arrête pas

check:
	@echo "━━━━━━━━━━━━━━━━━━━━━━"
	@echo "🔍 STATUS CHECK"
	@echo "━━━━━━━━━━━━━━━━━━━━━━"

	@echo "Symfony:"
	@symfony server:status > /dev/null 2>&1 && echo "  ✅ Running" || echo "  ❌ Not running"

	@echo "Mercure:"
	@ps aux | grep mercure | grep -v grep > /dev/null && echo "  ✅ Running" || echo "  ❌ Not running"
