#!groovy

pipeline {
    agent any

    stages {
        stage('Setup') {
            steps {
                sh '''
                    pwd && \
                    whoami && \
                    echo $WORKSPACE && \
                    ls -la
                '''
            }
            post {
                always {
                    step([$class: 'WsCleanup'])
                }
            }
        }
        stage('Browser Tests setup') {
            agent {
                docker {
                    image 'joomlaprojects/docker-systemtests'
                    args  '--user 0 --privileged=true'
                }
            }
            environment {
                CLOUD_NAME= 'redcomponent'
                API_KEY= '365447364384436'
                API_SECRET='Q94UM5kjZkZIrau8MIL93m0dN6U'
                GITHUB_TOKEN='4d92f9e8be0eddc0e54445ff45bf1ca5a846b609'
                ORGANIZATION='redCOMPONENT-COM'
                REPO='redSHOP'
            }
            steps {
                sh 'bash build/jenkins/system-tests.sh'
            }
            post {
                always {
                    step([$class: 'WsCleanup'])
                }
            }
        }
    }
}