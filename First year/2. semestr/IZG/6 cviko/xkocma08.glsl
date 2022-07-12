#ifdef VERTEX_SHADER
//#define MAKE_STUDENT_RELEASE
uniform mat4 view;
uniform mat4 proj;
#line 5


out highp vec2 vCoord;
out highp vec3 vCoord3;
flat out int matrialId;

void drawGnd(int vId, mat4 mvp, int material) {
  if(gl_VertexID==0){
    matrialId = material;
    vCoord = vec2(0,0);
    gl_Position = mvp*vec4(-2,0,-2,1);
  }
  if(gl_VertexID==1){
    matrialId = material;
    vCoord = vec2(2,0);
    gl_Position = mvp*vec4(2,0,-2,1);
  }
  if(gl_VertexID==2){
    matrialId = material;
    vCoord = vec2(0,2);
    gl_Position = mvp*vec4(-2,0,2,1);
  }

  if(gl_VertexID==3){
    matrialId = material;
    vCoord = vec2(0,2);
    gl_Position = mvp*vec4(-2,0,2,1);
  }
  if(gl_VertexID==4){
    matrialId = material;
    vCoord = vec2(2,0);
    gl_Position = mvp*vec4(2,0,-2,1);
  }
  if(gl_VertexID==5){
    matrialId = material;
    vCoord = vec2(2,2);
    gl_Position = mvp*vec4(2,0,2,1);
  }
}

void DrawRoof(int vId, mat4 mvp, int material){
  if(gl_VertexID==6){
    matrialId = material;
    vCoord3 = vec3(0,2, 0);
    gl_Position = mvp*vec4(0, 1, 0, 0.5);
  }
  if(gl_VertexID==7){
    matrialId = material;
    vCoord3 = vec3(0.5, 1, 0.5);
    gl_Position = mvp*vec4(0.5, 1, 0.5, 1);
  }
  if(gl_VertexID==8){
    matrialId = material;
    vCoord3 = vec3(0.5,1, -0.5);
    gl_Position = mvp*vec4(-0.5, 1, 0.5, 1);
  }

  if(gl_VertexID==9){
    matrialId = material;
    vCoord3 = vec3(0, 2, 0);
    gl_Position = mvp*vec4(0, 2, 0, 1);
  }
  if(gl_VertexID==10){
    matrialId = material;
    vCoord3 = vec3(0.5, 1, -0.5);
    gl_Position = mvp*vec4(0.5, 1, -0.5, 1);
  }
  if(gl_VertexID==11){
    matrialId = material;
    vCoord3 = vec3(-0.5, 1, -0.5);
    gl_Position = mvp*vec4(-0.5, 1, -0.5, 1);
  }

  if (gl_VertexID == 12) {
    matrialId = material;
    vCoord3 = vec3(0, 2, 0);
    gl_Position = mvp*vec4(0, 2, 0, 1);
  }
  if (gl_VertexID == 13) {
    matrialId = material;
    vCoord3 = vec3(-0.5, 1, -0.5);
    gl_Position = mvp*vec4(-0.5, 1, -0.5, 1);
  }
  if (gl_VertexID == 14) {
    matrialId = material;
    vCoord3 = vec3(-0.5, 1, 0.5);
    gl_Position = mvp*vec4(-0.5, 1, 0.5, 1);
  }

  if (gl_VertexID == 15) {
    matrialId = material;
    vCoord3 = vec3(0, 2, 0);
    gl_Position = mvp*vec4(0, 2, 0, 1);
  }
  if (gl_VertexID == 16) {
    matrialId = material;
    vCoord3 = vec3(0.5, 1, 0.5);
    gl_Position = mvp*vec4(0.5, 1, 0.5, 1);
  }
  if (gl_VertexID == 17) {
    matrialId = material;
    vCoord3 = vec3(0.5, 1, -0.5);
    gl_Position = mvp*vec4(0.5, 1, -0.5, 1);
  }
}

void DrawHouse(int vId, mat4 mvp, int material){
  if(gl_VertexID == 18){
    matrialId = material;
    vCoord3 = vec3(0.5, 0, -0.5);
    gl_Position = mvp*vec4(0.5, 0, -0.5, 1);
  }
  if(gl_VertexID == 19){
    matrialId = material;
    vCoord3 = vec3(0.5, 0, 0.5);
    gl_Position = mvp*vec4(0.5, 0, 0.5, 1);
  }
  if(gl_VertexID == 20){
    matrialId = material;
    vCoord3 = vec3(0.5, 1, -0.5);
    gl_Position = mvp*vec4(0.5, 1, -0.5, 1);
  }

  if(gl_VertexID == 21){
    matrialId = material;
    vCoord3 = vec3(0.5, 1, -0.5);
    gl_Position = mvp*vec4(0.5, 1, -0.5, 1);
  }
  if(gl_VertexID == 22){
    matrialId = material;
    vCoord3 = vec3(0.5, 1, 0.5);
    gl_Position = mvp*vec4(0.5, 1, 0.5, 1);
  }
  if(gl_VertexID == 23){
    matrialId = material;
    vCoord3 = vec3(0.5, 0, 0.5);
    gl_Position = mvp*vec4(0.5, 0, 0.5, 1);
  }

  if(gl_VertexID == 24){
    matrialId = material;
    vCoord3 = vec3(0.5, 0, 0.5);
    gl_Position = mvp*vec4(0.5, 0, 0.5, 1);
  }
  if(gl_VertexID == 25){
    matrialId = material;
    vCoord3 = vec3(0.5, 1, 0.5);
    gl_Position = mvp*vec4(0.5, 1, 0.5, 1);
  }
  if(gl_VertexID == 26){
    matrialId = material;
    vCoord3 = vec3(-0.5, 0, 0.5);
    gl_Position = mvp*vec4(-0.5, 0, 0.5, 1);
  }
  
  if(gl_VertexID == 27){
    matrialId = material;
    vCoord3 = vec3(-0.5, 0, 0.5);
    gl_Position = mvp*vec4(-0.5, 0, 0.5, 1);
  }
  if(gl_VertexID == 28){
    matrialId = material;
    vCoord3 = vec3(0.5, 1, 0.5);
    gl_Position = mvp*vec4(0.5, 1, 0.5, 1);
  }
  if(gl_VertexID == 29){
    matrialId = material;
    vCoord3 = vec3(-0.5, 1, 0.5);
    gl_Position = mvp*vec4(-0.5, 1, 0.5, 1);
  }

  if(gl_VertexID == 30){
    matrialId = material;
    vCoord3 = vec3(-0.5, 1, 0.5);
    gl_Position = mvp*vec4(-0.5, 1, 0.5, 1);
  }
  if(gl_VertexID == 31){
    matrialId = material;
    vCoord3 = vec3(-0.5, 0, 0.5);
    gl_Position = mvp*vec4(-0.5, 0, 0.5, 1);
  }
  if(gl_VertexID == 32){
    matrialId = material;
    vCoord3 = vec3(-0.5, 0, -0.5);
    gl_Position = mvp*vec4(-0.5, 0, -0.5, 1);
  }

  if(gl_VertexID == 33){
    matrialId = material;
    vCoord3 = vec3(-0.5, 1, 0.5);
    gl_Position = mvp*vec4(-0.5, 1, 0.5, 1);
  }
  if(gl_VertexID == 34){
    matrialId = material;
    vCoord3 = vec3(-0.5, 0, 0.5);
    gl_Position = mvp*vec4(-0.5, 1, -0.5, 1);
  }
  if(gl_VertexID == 35){
    matrialId = material;
    vCoord3 = vec3(-0.5, 0, -0.5);
    gl_Position = mvp*vec4(-0.5, 0, -0.5, 1);
  }

  if(gl_VertexID == 36){
    matrialId = material;
    vCoord3 = vec3(-0.5, 0, -0.5);
    gl_Position = mvp*vec4(-0.5, 0, -0.5, 1);
  }
  if(gl_VertexID == 37){
    matrialId = material;
    vCoord3 = vec3(-0.5, 0, 0.5);
    gl_Position = mvp*vec4(-0.5, 1, -0.5, 1);
  }
  if(gl_VertexID == 38){
    matrialId = material;
    vCoord3 = vec3(0.5, 0, -0.5);
    gl_Position = mvp*vec4(0.5, 0, -0.5, 1);
  }
  
  if(gl_VertexID == 39){
    matrialId = material;
    vCoord3 = vec3(0.5, 0, -0.5);
    gl_Position = mvp*vec4(0.5, 1, -0.5, 1);
  }
  if(gl_VertexID == 40){
    matrialId = material;
    vCoord3 = vec3(-0.5, 0, 0.5);
    gl_Position = mvp*vec4(-0.5, 1, -0.5, 1);
  }
  if(gl_VertexID == 41){
    matrialId = material;
    vCoord3 = vec3(0.5, 0, -0.5);
    gl_Position = mvp*vec4(0.5, 0, -0.5, 1);
  }
}

void main() {
  if (gl_VertexID > 42){
    return;
  }
  mat4 mvp = proj*view;
  drawGnd(gl_VertexID, mvp, 0);
  DrawRoof(gl_VertexID, mvp, 1);
  DrawHouse(gl_VertexID, mvp, 2);
}
#endif



#ifdef FRAGMENT_SHADER
flat in int matrialId;
out highp vec4 fColor;
in highp vec2 vCoord;
in highp vec3 vCoord3;

highp vec4 ground(highp vec2 uv){
  for (float x = -2.0; x < 2.0; x += 0.5){
    for (float y = -2.0; y < 2.0; y += 0.5){
      highp float dist = distance(uv,vec2(x+0.75, y+0.75));
      if (dist < 0.25){
        return vec4(0.0, 1.0, 0.0, 1.0);
      }
    }
  }
  return vec4(0.0, 0.5, 0.0, 1.0);
}

highp vec4 grad(highp vec3 uv){
  int a = 0;
  highp float dist = distance(uv,vec3(0, 2, 0));
  if (dist < sqrt((0.5*0.5) + (1*1))){
    return vec4(uv[1] - 1 ,0 ,0 , 1);
  }
  return vec4(0,0,0,1);
}

highp vec4 windows(highp vec3 uv){
  if ((uv.y >= 0.1 && uv.y <= 0.9) && (uv.x >= -0.4 && uv.x <= 0.4) && ((uv.z == 0.5) || (uv.z == -0.5))){
    return vec4(0.5, 0.5, 0, 0.5);
  }
  if ((uv.y >= 0.1 && uv.y <= 0.9) && (uv.z >= -0.4 && uv.z <= 0.4) && ((uv.x == 0.5) || (uv.x == -0.5)))
  {
    return vec4(0.5, 0.5, 0, 0.5);
  } 
  return vec4(0, 0, 0, 1);
}

void main() {
    if (matrialId == 0)
      fColor = ground(vCoord);
    if (matrialId == 1){
      fColor = grad(vCoord3);
    }
    if (matrialId == 2){
      fColor = windows(vCoord3);
    }
}
#endif