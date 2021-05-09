# set base image (host OS)
FROM python:3.8

#
COPY . /app

WORKDIR /app

RUN pip install -r requirements.txt

EXPOSE 5001

ENTRYPOINT [ "python" ]

CMD [ "main.py" ]


#COPY ./requirements.txt /app/requirements.txt
