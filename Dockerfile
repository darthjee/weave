FROM python:3.14-slim-bullsey

RUN useradd -u 1000 app; \
    mkdir -p /home/app/app; \
    chown app.app -R /home/app

COPY ./source/ /home/app/app/

WORKDIR /home/app/app/

USER app

CMD ["bash"]
