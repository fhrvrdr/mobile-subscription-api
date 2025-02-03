# Çok aşamalı build için birinci aşama
FROM golang:1.23.3-alpine AS builder

# Gerekli build araçlarını yükle
RUN apk add --no-cache git

WORKDIR /app

# Önce go.mod dosyasını oluşturalım
COPY . .

# Uygulamayı derle
RUN CGO_ENABLED=0 GOOS=linux go build -a -installsuffix cgo -o main .

# İkinci aşama - minimal runtime image
FROM alpine:latest

WORKDIR /root/

# Alpine'da çalışması için gerekli SSL sertifikalarını ve tzdata'yı ekleyelim
RUN apk --no-cache add ca-certificates tzdata

# Sadece derlenmiş uygulamayı kopyala
COPY --from=builder /app/main .

# Çalıştırma izinlerini ayarla
RUN chmod +x /root/main

EXPOSE 3000

# Tam yolu belirterek çalıştır
CMD ["/root/main"] 