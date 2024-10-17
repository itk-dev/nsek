# https://hub.docker.com/r/linuxserver/libreoffice
FROM linuxserver/libreoffice:7.6.7

# https://github.com/unoconv/unoconv#installing-unoconv
# RUN apk add --update --no-cache unoconv npm

# https://github.com/sfoxdev/docker-unoconv-alpine/blob/master/Dockerfile
ENV UNO_URL https://raw.githubusercontent.com/dagwieers/unoconv/master/unoconv

# Install Unoconv and Microsoft fonts (e.g. Arial)
RUN apk add --update --no-cache npm msttcorefonts-installer fontconfig \
    && wget $UNO_URL -O /usr/local/bin/unoconv \
    && chmod +x /usr/local/bin/unoconv \
    && update-ms-fonts \
    && fc-cache -f

# TODO: Install distutils via setuptools
# linuxserver/libreoffice:7.6.7 comes with python 3.12
# which does not come with distutils
# cf. https://docs.python.org/3/whatsnew/3.12.html

# Unoconv is deprecated (cf.
# https://github.com/unoconv/unoconv#unoconv-is-deprecated), but works great. In
# the future we should use https://github.com/unoconv/unoserver/ and
# https://github.com/damian66/node-unoserver

# https://github.com/unoconv/unoserver#installation
# https://stackoverflow.com/a/62555259
# ENV PYTHONUNBUFFERED=1
# RUN apk add --update --no-cache python3 npm \
#     && python3 -m ensurepip \
#     && pip3 install --no-cache --upgrade pip unoserver

EXPOSE 9980

WORKDIR /app

COPY app/server.js app/package*.json /app/
RUN npm install

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

CMD [ "docker-entrypoint.sh" ]
